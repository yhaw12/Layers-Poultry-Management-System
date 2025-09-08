<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\AlertRule;
use App\Models\User;
use App\Models\UserActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        // keep admin-only access
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Show activity logs with filters, pagination and optional export (csv|pdf)
     */
    public function index(Request $request)
    {
        try {
            // Validate light inputs (safe-guard)
            $request->validate([
                'search'     => 'nullable|string|max:255',
                'start_date' => 'nullable|date',
                'end_date'   => 'nullable|date|after_or_equal:start_date',
                'user_id'    => 'nullable|exists:users,id',
                'action'     => 'nullable|string',
                'per_page'   => 'nullable|integer|min:1|max:200',
                'export'     => 'nullable|in:csv,pdf',
            ]);
        } catch (\Exception $e) {
            Log::warning('ActivityLogController: validation failed for index', ['err' => $e->getMessage(), 'input' => $request->all()]);
            return back()->withErrors(['Invalid filter input.'])->withInput();
        }

        $perPage = (int) $request->input('per_page', 15);
        $search  = $request->input('search');
        $start = $request->input('start_date', now()->subMonths(1)->startOfMonth()->toDateString());
        $end   = $request->input('end_date', now()->endOfMonth()->toDateString());
        $userId  = $request->input('user_id');
        $action  = $request->input('action');
        $export  = $request->input('export');


        // build base query
        $baseQuery = UserActivityLog::with('user')->when($search, function ($q, $search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('action', 'like', "%{$search}%")
                       ->orWhere('details', 'like', "%{$search}%")
                       ->orWhereHas('user', function ($u) use ($search) {
                           $u->where('name', 'like', "%{$search}%");
                       });
                });
            })
            ->when($start, function ($q, $s) {
                // ensure start is parsed correctly
                $d = Carbon::parse($s)->startOfDay()->toDateTimeString();
                $q->where('created_at', '>=', $d);
            })
            ->when($end, function ($q, $e) {
                $d = Carbon::parse($e)->endOfDay()->toDateTimeString();
                $q->where('created_at', '<=', $d);
            })
            ->when($userId, function ($q, $uid) {
                $q->where('user_id', $uid);
            })
            ->when($action, function ($q, $act) {
                $q->where('action', $act);
            });

        // Export requested? generate full dataset (no pagination)
        if ($export) {
            try {
                $items = (clone $baseQuery)->orderBy('created_at', 'desc')->get();

                if ($export === 'csv') {
                    $filename = 'activity_logs_' . now()->format('Ymd_His') . '.csv';
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename={$filename}",
                    ];

                    // Use php://temp and w+ (safe)
                    $fp = fopen('php://temp', 'w+');
                    // header row
                    fputcsv($fp, ['Date', 'User', 'Action', 'Details']);

                    foreach ($items as $it) {
                        fputcsv($fp, [
                            $it->created_at ? $it->created_at->format('Y-m-d H:i:s') : '',
                            $it->user ? $it->user->name : 'System',
                            $it->action,
                            $it->details,
                        ]);
                    }

                    rewind($fp);
                    $content = stream_get_contents($fp);
                    fclose($fp);

                    return response($content, 200, $headers);
                }

                if ($export === 'pdf') {
                    // You need barryvdh/laravel-dompdf installed for this
                    $pdf = Pdf::loadView('activity-logs.export_pdf', [
                        'items' => $items,
                        'filters' => $request->only(['search','start_date','end_date','user_id','action']),
                    ])->setPaper('a4', 'landscape');

                    return $pdf->download('activity_logs_' . now()->format('Ymd_His') . '.pdf');
                }
            } catch (\Exception $e) {
                Log::error('ActivityLogController: export failed', ['err' => $e->getMessage(), 'input' => $request->all(), 'user' => Auth::id()]);
                return back()->with('error', 'Failed to export activity logs: ' . $e->getMessage());
            }
        }

        // No export — paginate and show view
        $logs = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString(); // preserves filters in pagination links

        // data for selects
        $users = User::orderBy('name')->get(['id', 'name']);
        $actions = UserActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('activity-logs.index', compact('logs', 'users', 'actions'));
    }

    /**
     * Mark a single alert as read (unchanged)
     */
    public function read(Request $request, Alert $alert)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($alert->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $alert->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Alert marked as read.');
    }

    /**
     * Dismiss all unread alerts for current user (unchanged but with extra logging)
     */
    public function dismissAll(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            Log::info('Dismiss all alerts attempt', ['user_id' => $user->id, 'is_admin' => $user->hasRole('admin')]);

            $count = Alert::where('user_id', $user->id)->whereNull('read_at')->count();

            if ($count === 0) {
                Log::info('No unread alerts to dismiss', ['user_id' => $user->id]);
                return redirect()->back()->with('success', 'No unread alerts to dismiss.');
            }

            Alert::where('user_id', $user->id)->whereNull('read_at')->update(['read_at' => now()]);

            Log::info("Dismissed {$count} unread alerts", ['user_id' => $user->id]);

            return redirect()->back()->with('success', "Dismissed {$count} alerts.");
        } catch (\Exception $e) {
            Log::error('Failed to dismiss alerts', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Failed to dismiss alerts: ' . $e->getMessage());
        }
    }

    /**
     * Create a custom alert rule (kept simple; you had this partly stubbed)
     */
    public function createCustom(Request $request)
    {
        $validated = $request->validate([
            'condition' => 'required|string',
            'message'   => 'required|string',
        ]);

        // TODO: implement persisting rules, for now we log and return success as before
        // AlertRule::create(array_merge($validated, ['user_id' => auth()->id()]));
        Log::info('Custom alert rule created (stub)', ['user_id' => Auth::id(), 'rule' => $validated]);

        return redirect()->back()->with('success', 'Custom alert created.');
    }
}
