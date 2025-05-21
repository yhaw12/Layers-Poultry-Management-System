<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Egg;

class EggController extends Controller
{
    public function index()
    {
        $eggs = Egg::all();

        // Monthly egg crate chart data (last 6 months)
        $eggData = [];
        $eggLabels = [];
        for ($i = 0; $i < 6; $i++) {
            $month = now()->subMonths($i);
            $eggLabels[] = $month->format('M Y');
            $eggData[] = Egg::whereMonth('date_laid', $month->month)
                            ->whereYear('date_laid', $month->year)
                            ->sum('crates');
        }
        $eggLabels = array_reverse($eggLabels);
        $eggData = array_reverse($eggData);

        return view('eggs.index', compact('eggs', 'eggLabels', 'eggData'));
    }

    public function create()
    {
        return view('eggs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'crates' => 'required|integer|min:0',
            'date_laid' => 'required|date',
            'sold_quantity' => 'nullable|integer|min:0',
            'sold_date' => 'nullable|date',
            'sale_price' => 'nullable|numeric|min:0',
        ]);

        Egg::create($data);
        return redirect()->route('eggs.index')->with('success', 'Egg record added successfully');
    }

    public function edit(Egg $egg)
    {
        return view('eggs.edit', compact('eggs'));
    }

    public function update(Request $request, Egg $egg)
    {
        $data = $request->validate([
            'crates' => 'required|integer|min:0',
            'date_laid' => 'required|date',
            'sold_quantity' => 'nullable|integer|min:0',
            'sold_date' => 'nullable|date',
            'sale_price' => 'nullable|numeric|min:0',
        ]);

        $egg->update($data);
        return redirect()->route('eggs.index')->with('success', 'Egg record updated successfully');
    }

    public function destroy(Egg $egg)
    {
        $egg->delete();
        return redirect()->route('eggs.index')->with('success', 'Egg record deleted successfully');
    }
}