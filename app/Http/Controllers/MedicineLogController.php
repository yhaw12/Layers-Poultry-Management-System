<?php

namespace App\Http\Controllers;

use App\Models\MedicineLog;
use Illuminate\Http\Request;

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

    public function destroy(MedicineLog $medicineLog)
    {
        $medicineLog->delete();
        return redirect()->route('medicine-logs.index')->with('success', 'Medicine log deleted successfully');
    }
}