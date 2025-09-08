<?php

namespace App\Http\Controllers;

use App\Models\VaccinationLog;
use App\Models\Bird;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VaccinationLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'nullable|date|before_or_equal:end_date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());
            $cacheKey = "vaccination_logs_{$start}_{$end}";

            $logs = Cache::remember($cacheKey, 3, function () use ($start, $end) {
                return VaccinationLog::with('bird')
                    ->whereBetween('date_administered', [$start, $end])
                    ->whereNull('deleted_at')
                    ->orderBy('date_administered', 'desc')
                    ->paginate(10);
            });

            return view('vaccination-logs.index', compact('logs', 'start', 'end'));
        } catch (\Exception $e) {
            Log::error('Failed to load vaccination logs', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load vaccination logs.');
        }
    }

    public function create()
    {
        $birds = Bird::whereNull('deleted_at')->get();
        return view('vaccination-logs.create', compact('birds'));
    }

    public function store(Request $request)
{
    try {
        \Log::info('Vaccination log store request: ', $request->all());
        $validated = $request->validate([
            'bird_id' => 'required|exists:birds,id',
            'vaccine_name' => 'required|string|max:255',
            'date_administered' => 'required|date',
            'notes' => 'nullable|string',
            'next_vaccination_date' => 'nullable|date|after:date_administered',
        ]);

        $log = VaccinationLog::create($validated);

        \App\Models\UserActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'created_vaccination_log',
            'details' => "Created vaccination log for bird ID {$validated['bird_id']} on {$validated['date_administered']}",
        ]);

        return redirect()->route('vaccination-logs.index')->with('success', 'Vaccination log added.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed for vaccination log', ['errors' => $e->errors(), 'request' => $request->all()]);
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        Log::error('Failed to store vaccination log', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'request' => $request->all()]);
        return back()->with('error', 'Failed to add vaccination log.');
    }
}
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'bird_id' => 'required|exists:birds,id',
                'vaccine_name' => 'required|string|max:255',
                'date_administered' => 'required|date',
                'notes' => 'nullable|string',
                'next_vaccination_date' => 'nullable|date|after:date_administered',
            ]);

            $log = VaccinationLog::findOrFail($id);
            $log->update($validated);

            \App\Models\UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'updated_vaccination_log',
                'details' => "Updated vaccination log for bird ID {$validated['bird_id']} on {$validated['date_administered']}",
            ]);

            return redirect()->route('vaccination-logs.index')->with('success', 'Vaccination log updated.');
        } catch (\Exception $e) {
            Log::error('Failed to update vaccination log', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to update vaccination log.');
        }
    }

    public function destroy(Request $request ,$id)
    {
        try {
            $vaccine = VaccinationLog::findOrFail($id);
           

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'deleted_vaccination_log',
                'details' => "Deleted vaccination log ID {$id}",
            ]);

             $vaccine->delete();

              if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vaccine  deleted successfully.'
                ], 200);
            }

            return redirect()->route('vaccination-logs.index')->with('success', 'Vaccination log deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete vaccination log', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

             if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete vaccine log. ' . ($e->getCode() == 23000 ? 'This vaccine is linked to other data.' : 'Please try again.')
                ], 500);
            }
            return redirect()->route('vaccination-logs.index')->with('error', 'Failed to delete vaccine log.');
        }
    }

        public function edit($id)
{
    try {
        $log = VaccinationLog::with('bird')->findOrFail($id);
        $birds = Bird::whereNull('deleted_at')->get();

        return view('vaccination-logs.edit', compact('log', 'birds'));
    } catch (\Exception $e) {
        
        return redirect()->route('vaccination-logs.index')
            ->with('error', 'Failed to load vaccination log for editing.');
    }
}

    public function reminders()
    {
        try {
            $reminders = VaccinationLog::where('next_vaccination_date', '<=', now()->addDays(7))
                ->where('next_vaccination_date', '>=', now())
                ->whereNull('deleted_at')
                ->with('bird')
                ->get();
            return view('vaccination-logs.reminders', compact('reminders'));
        } catch (\Exception $e) {
            Log::error('Failed to load vaccination reminders', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load vaccination reminders.');
        }
    }
}