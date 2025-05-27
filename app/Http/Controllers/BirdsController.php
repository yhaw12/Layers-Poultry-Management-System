<?php

namespace App\Http\Controllers;

use App\Models\Bird;
use Illuminate\Http\Request;

class BirdsController extends Controller
{
    public function index()
    {
        $birds = Bird::paginate(10);
        $totalQuantity = Bird::sum('quantity') ?? 0;
        $layers = Bird::where('type', 'layer')->sum('quantity') ?? 0;
        $broilers = Bird::where('type', 'broiler')->sum('quantity') ?? 0;
        return view('birds.index', compact('birds', 'totalQuantity', 'layers', 'broilers'));
    }

    public function create()
    {
        return view('birds.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
    'breed' => 'required|string|max:255',
    'type' => 'required|in:layer,broiler',
    'quantity' => 'required|integer|min:1',
    'working' => 'required|boolean',
    'age' => 'required|integer|min:0',
    'entry_date' => 'required|date',
    'vaccination_status' => 'nullable|string|max:255',
    'housing_location' => 'nullable|string|max:255',
    'stage' => 'required|in:chick,grower,layer',
    ]);
        Bird::create($validated);
        return redirect()->route('birds.index')->with('success', 'Bird batch added successfully.');
    }

    public function edit(Bird $bird)
    {
        return view('birds.edit', compact('bird'));
    }

    public function update(Request $request, Bird $bird)
    {
        $validated = $request->validate([
            'breed' => 'required|string|max:255',
            'type' => 'required|in:layer,broiler',
            'quantity' => 'required|integer|min:1',
            'working' => 'required|boolean',
            'age' => 'required|integer|min:0',
            'entry_date' => 'required|date',
        ]);
        $bird->update($validated);
        return redirect()->route('birds.index')->with('success', 'Bird batch updated successfully.');
    }

    public function destroy(Bird $bird)
    {
        $bird->delete();
        return redirect()->route('birds.index')->with('success', 'Bird batch deleted successfully.');
    }
}