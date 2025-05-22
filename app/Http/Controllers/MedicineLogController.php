<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MedicineLog;

class MedicineLogController extends Controller
{
    public function index()
    {
        $logs = MedicineLog::orderBy('date', 'desc')->paginate(10);
        return view('medicine_logs.index', compact('logs'));
    }

    public function create()
    {
        return view('medicine_logs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_name' => 'required|string|max:255',
            'type' => 'required|in:purchase,consumption',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:10',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        MedicineLog::create($request->all());
        return redirect()->route('medicine-logs.index')->with('success', 'Log added successfully!');
    }

    public function edit(MedicineLog $medicineLog)
    {
        return view('medicine_logs.edit', compact('medicineLog'));
    }

    public function update(Request $request, MedicineLog $medicineLog)
    {
        $request->validate([
            'medicine_name' => 'required|string|max:255',
            'type' => 'required|in:purchase,consumption',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:10',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $medicineLog->update($request->all());
        return redirect()->route('medicine-logs.index')->with('success', 'Log updated successfully!');
    }

    public function destroy(MedicineLog $medicineLog)
    {
        $medicineLog->delete();
        return redirect()->route('medicine-logs.index')->with('success', 'Log deleted.');
    }
}
