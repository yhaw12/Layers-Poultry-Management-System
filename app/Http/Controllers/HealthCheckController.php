<?php

namespace App\Http\Controllers;

use App\Models\Bird;
use App\Models\HealthCheck;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HealthCheckController extends Controller
{
    public function index()
    {
        $healthChecks = HealthCheck::with('bird')->orderBy('date', 'desc')->paginate(10);
        return view('health-checks.index', compact('healthChecks'));
    }

    public function create()
    {
        $birds = Bird::all();
        return view('health-checks.create', compact('birds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bird_id' => 'required|exists:birds,id',
            'date' => 'required|date',
            'status' => 'required|string|max:255',
            'symptoms' => 'nullable|string',
            'treatment' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        HealthCheck::create($validated);
        return redirect()->route('health-checks.index')->with('success', 'Health check logged successfully.');
    }


    public function destroy(Request $request, $id)
    {
        try {
            $healthCheck = HealthCheck::findOrFail($id);

            // Log the activity
            UserActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'action' => 'deleted_health_check',
                'details' => "Deleted health check for bird breed " . ($healthCheck->bird->breed ?? 'N/A') . " on {$healthCheck->date} with status {$healthCheck->status}",
            ]);

            // Delete the health check (soft delete if enabled)
            $healthCheck->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Health check record deleted successfully.'
                ], 200);
            }

            return redirect()->route('health-checks.index')->with('success', 'Health check record deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete health check: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete health check record. ' . ($e->getCode() == 23000 ? 'This health check is linked to other data.' : 'Please try again.')
                ], 500);
            }

            return redirect()->route('health-checks.index')->with('error', 'Failed to delete health check record.');
        }
    }

    // public function getHealthChecksTable(Request $request)
    // {
    //     $healthChecks = HealthCheck::with('bird')->paginate(10); // Adjust per_page as needed
    //     $tableHtml = view('health-checks-index', compact('healthChecks'))->renderSections()['table-content'];

    //     return response()->json([
    //         'success' => true,
    //         'table' => $tableHtml
    //     ]);
    // }
}
