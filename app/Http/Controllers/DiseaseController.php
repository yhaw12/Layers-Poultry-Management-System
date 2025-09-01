<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\HealthCheck;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DiseaseController extends Controller
{
    public function index()
    {
        $diseases = Disease::orderBy('name')->paginate(10);
        return view('diseases.index', compact('diseases'));
    }

   public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all()),
                    ], 422);
                }
                return redirect()->route('diseases.index')->withErrors($validator)->withInput();
            }

            $disease = Disease::create([
                'name' => $request->input('name'),
            ]);

            // Log the activity
            UserActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'action' => 'created_disease',
                'details' => "Added disease '{$disease->name}'",
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Disease added successfully.'
                ], 200);
            }

            return redirect()->route('diseases.index')->with('success', 'Disease added successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to add disease: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add disease. Please try again.'
                ], 500);
            }

            return redirect()->route('diseases.index')->with('error', 'Failed to add disease.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $disease = Disease::findOrFail($id);

            // Log the activity
            UserActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'action' => 'deleted_disease',
                'details' => "Deleted disease '{$disease->name}'",
            ]);

            // Delete the disease (soft delete if enabled)
            $disease->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Disease record deleted successfully.'
                ], 200);
            }

            return redirect()->route('diseases.index')->with('success', 'Disease record deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete disease: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete disease record. ' . ($e->getCode() == 23000 ? 'This disease is linked to other data.' : 'Please try again.')
                ], 500);
            }

            return redirect()->route('diseases.index')->with('error', 'Failed to delete disease record.');
        }
    }

    // public function getDiseasesTable(Request $request)
    // {
    //     $diseases = Disease::query()->paginate(10); // Adjust per_page as needed
    //     $tableHtml = view('diseases-index', compact('diseases'))->renderSections()['table-content'];

    //     return response()->json([
    //         'success' => true,
    //         'table' => $tableHtml
    //     ]);
    // }

    public function history(Disease $disease)
    {
        $history = HealthCheck::where('disease_id', $disease->id)
            ->with('bird')
            ->orderBy('date', 'desc')
            ->paginate(10);
        return view('diseases.history', compact('disease', 'history'));
    }
}


