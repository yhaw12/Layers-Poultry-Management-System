<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'is_admin']);
    }

    public function index(Request $request)
    {
        $logs = UserActivityLog::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('action', 'like', "%{$search}%")
                      ->orWhere('details', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('activity-logs.index', compact('logs'));
    }

    public function read(Request $request, Alert $alert)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($alert->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Log::info('Marking alert as read', ['alert_id' => $alert->id, 'user_id' => $user->id]);
        $alert->update(['read_at' => now()]);
        return redirect()->back()->with('success', 'Alert marked as read.');
    }

    public function dismissAll(Request $request)
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            Log::info('Dismiss all alerts attempt', ['user_id' => $user->id, 'is_admin' => $user->isAdmin()]);
            $count = Alert::where('user_id', $user->id)->whereNull('read_at')->count();
            if ($count === 0) {
                Log::info('No unread alerts to dismiss', ['user_id' => $user->id]);
                return redirect()->back()->with('success', 'No unread alerts to dismiss.');
            }
            Alert::where('user_id', $user->id)->whereNull('read_at')->update(['read_at' => now()]);
            Log::info("Dismissed $count unread alerts", ['user_id' => $user->id]);
            return redirect()->back()->with('success', "Dismissed $count alerts.");
        } catch (\Exception $e) {
            Log::error('Failed to dismiss alerts', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Failed to dismiss alerts: ' . $e->getMessage());
        }
    }
}