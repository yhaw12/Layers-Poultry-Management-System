<?php

namespace App\Http\Controllers;

use App\Models\MedicineLog;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MedicineLogController extends Controller
{
    public function index()
    {
        $medicineLogs = MedicineLog::orderBy('date', 'desc')->paginate(10);
        return view('medicine-logs.index', compact('medicineLogs'));
    }

    public function create()
    {
        return view('medicine-logs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'medicine_name' => 'required|string|max:255',
            'type' => 'required|in:purchase,consumption',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        MedicineLog::create($data);
        return redirect()->route('medicine-logs.index')->with('success', 'Medicine log added successfully');
    }

    public function edit(MedicineLog $medicineLog)
    {
        return view('medicine-logs.edit', compact('medicineLog'));
    }

    public function update(Request $request, MedicineLog $medicineLog)
    {
        $data = $request->validate([
            'medicine_name' => 'required|string|max:255',
            'type' => 'required|in:purchase,consumption',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $medicineLog->update($data);
        return redirect()->route('medicine-logs.index')->with('success', 'Medicine log updated successfully');
    }

    public function destroy(Request $request, $id)
{
    try {
        $log = MedicineLog::findOrFail($id);

        // Log the activity (if applicable)
        UserActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'action' => 'deleted_medicine_log',
            'details' => "Deleted medicine log for {$log->medicine_name} (Quantity: {$log->quantity} {$log->unit}) on {$log->date}",
        ]);

        // Delete the medicine log (soft delete if enabled)
        $log->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Medicine log deleted successfully.'
            ], 200);
        }

        return redirect()->route('medicine-logs.index')->with('success', 'Medicine log deleted successfully.');
    } catch (\Exception $e) {
        Log::error('Failed to delete medicine log: ' . $e->getMessage());

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete medicine log. ' . ($e->getCode() == 23000 ? 'This log is linked to other data.' : 'Please try again.')
            ], 500);
        }

        return redirect()->route('medicine-logs.index')->with('error', 'Failed to delete medicine log.');
    }
}
}