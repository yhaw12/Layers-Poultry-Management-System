<?php

namespace App\Http\Controllers;

use App\Models\Hen;
use Illuminate\Http\Request;

class HenController extends Controller
{
    public function index()
    {
        $hens = Hen::all();

        // Monthly hen totals (last 6 months)
        $henData = [];
        $henLabels = [];
        for ($i = 0; $i < 6; $i++) {
            $month = now()->subMonths($i);
            $henLabels[] = $month->format('M Y');
            $henData[] = Hen::whereMonth('entry_date', $month->month)
                            ->whereYear('entry_date', $month->year)
                            ->sum('quantity');
        }
        $henLabels = array_reverse($henLabels);
        $henData = array_reverse($henData);

        return view('hen.index', compact('hens', 'henLabels', 'henData'));
    }

    public function create()
    {
        return view('hen.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'breed' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'working' => 'required|integer|min:0',
            'age' => 'required|integer|min:0',
            'entry_date' => 'required|date',
        ]);

        Hen::create($data);
        return redirect()->route('hens.index')->with('success', 'Hens added successfully');
    }

    public function edit(Hen $hen)
    {
        return view('hen.edit', compact('hen'));
    }

    public function update(Request $request, Hen $hen)
    {
        $data = $request->validate([
            'breed' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'working' => 'required|integer|min:0',
            'age' => 'required|integer|min:0',
            'entry_date' => 'required|date',
        ]);

        $hen->update($data);
        return redirect()->route('hen.index')->with('success', 'Hen record updated successfully');
    }

    public function destroy(Hen $hen)
    {
        $hen->delete();
        return redirect()->route('hen.index')->with('success', 'Hen record deleted successfully');
    }
}