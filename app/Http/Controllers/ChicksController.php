<?php

namespace App\Http\Controllers;

use App\Models\Chicks;
use Illuminate\Http\Request;

class ChicksController extends Controller
{
    public function index()
    {
        $chicks = Chicks::paginate(10);
        $totalQuantity = Chicks::sum('quantity_bought') ?? 0;
        return view('chicks.chicks', compact('chicks', 'totalQuantity'));
    }

    public function create()
    {
        return view('chicks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'breed' => 'required|string|max:255',
            'quantity_bought' => 'required|integer|min:1',
            'feed_amount' => 'required|numeric|min:0',
            'alive' => 'required|integer|min:0',
            'dead' => 'required|integer|min:0',
            'purchase_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
        ]);

        Chicks::create($validated);
        return redirect()->route('chicks.index')->with('success', 'Chick batch added successfully.');
    }

    public function edit(Chicks $chick)
    {
        return view('chicks.edit', compact('chick'));
    }

    public function update(Request $request, Chicks $chick)
    {
        $validated = $request->validate([
            'breed' => 'required|string|max:255',
            'quantity_bought' => 'required|integer|min:1',
            'feed_amount' => 'required|numeric|min:0',
            'alive' => 'required|integer|min:0',
            'dead' => 'required|integer|min:0',
            'purchase_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
        ]);

        $chick->update($validated);
        return redirect()->route('chicks.index')->with('success', 'Chick batch updated successfully.');
    }

    public function destroy(Chicks $chick)
    {
        $chick->delete();
        return redirect()->route('chicks.index')->with('success', 'Chick batch deleted successfully.');
    }
}