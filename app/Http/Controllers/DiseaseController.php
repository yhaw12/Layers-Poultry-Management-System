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

    public function create()
    {
        return view('diseases.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'start_date' => 'required|date',
                'description' => 'nullable|string',
                'symptoms' => 'nullable|string',
                'treatments' => 'nullable|string',
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
                'start_date' => $request->input('start_date'),
                'description' => $request->input('description'),
                'symptoms' => $request->input('symptoms'),
                'treatments' => $request->input('treatments'),
            ]);

            UserActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'action' => 'created_disease',
                'details' => "Added disease '{$disease->name}' on {$disease->start_date}",
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

    public function edit($id)
    {
        try {
            $disease = Disease::findOrFail($id);
            return view('diseases.edit', compact('disease'));
        } catch (\Exception $e) {
            Log::error('Failed to retrieve disease for editing: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve disease record.'
                ], 500);
            }
            return redirect()->route('diseases.index')->with('error', 'Failed to retrieve disease record.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $disease = Disease::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'start_date' => 'required|date',
                'description' => 'nullable|string',
                'symptoms' => 'nullable|string',
                'treatments' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()->toArray(),
                    ], 422);
                }
                return redirect()->route('diseases.edit', $id)->withErrors($validator)->withInput();
            }

            $disease->update([
                'name' => $request->input('name'),
                'start_date' => $request->input('start_date'),
                'description' => $request->input('description'),
                'symptoms' => $request->input('symptoms'),
                'treatments' => $request->input('treatments'),
            ]);

            UserActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'action' => 'updated_disease',
                'details' => "Updated disease '{$disease->name}' on {$disease->start_date}",
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Disease updated successfully.'
                ], 200);
            }

            return redirect()->route('diseases.index')->with('success', 'Disease updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update disease: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update disease record.'
                ], 500);
            }
            return redirect()->route('diseases.index')->with('error', 'Failed to update disease record.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $disease = Disease::findOrFail($id);

            UserActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'action' => 'deleted_disease',
                'details' => "Deleted disease '{$disease->name}'",
            ]);

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

    public function history(Disease $disease)
    {
        try {
            $history = $disease->healthChecks()
                ->with('bird')
                ->orderBy('date', 'desc')
                ->paginate(10);
            return view('diseases.history', compact('disease', 'history'));
        } catch (\Exception $e) {
            Log::error('Failed to retrieve disease history: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve disease history.'
                ], 500);
            }
            return redirect()->route('diseases.index')->with('error', 'Failed to retrieve disease history.');
        }
    }
}