<?php

namespace App\Http\Controllers;

use App\Models\Mortalities;
use Illuminate\Http\Request;

class MortalitiesController extends Controller
{
    public function index()
    {
        $mortalities = Mortalities::orderBy('date', 'desc')->paginate(10);
        $totalMortalities = Mortalities::sum('quantity') ?? 0;
        return view('mortalities.index', compact('mortalities', 'totalMortalities'));
    }

    public function create()
    {
        return view('mortalities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'cause' => 'nullable|string|max:255',
        ]);

        Mortalities::create($validated);
        return redirect()->route('mortalities.index')->with('success', 'Mortality record added successfully.');
    }

    public function edit(Mortalities $mortality)
    {
        return view('mortalities.edit', compact('mortality'));
    }

    public function update(Request $request, Mortalities $mortality)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'cause' => 'nullable|string|max:255',
        ]);

        $mortality->update($validated);
        return redirect()->route('mortalities.index')->with('success', 'Mortality record updated successfully.');
    }

    public function destroy(Mortalities $mortality)
    {
        $mortality->delete();
        return redirect()->route('mortalities.index')->with('success', 'Mortality record deleted successfully.');
    }
}