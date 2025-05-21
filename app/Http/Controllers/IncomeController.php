<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::all();

        // Monthly income comparison (last 6 months)
        $incomeData = [];
        $incomeLabels = [];
        for ($i = 0; $i < 6; $i++) {
            $month = now()->subMonths($i);
            $incomeLabels[] = $month->format('M Y');
            $incomeData[] = Income::whereMonth('date', $month->month)
                                  ->whereYear('date', $month->year)
                                  ->sum('amount');
        }
        $incomeLabels = array_reverse($incomeLabels);
        $incomeData = array_reverse($incomeData);

        return view('income.index', compact('incomes', 'incomeLabels', 'incomeData'));
    }

    public function create()
    {
        return view('income.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'source' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        Income::create($data);
        return redirect()->route('income.index')->with('success', 'Income added successfully');
    }

    public function edit(Income $income)
    {
        return view('income.edit', compact('income'));
    }

    public function update(Request $request, Income $income)
    {
        $data = $request->validate([
            'source' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $income->update($data);
        return redirect()->route('income.index')->with('success', 'Income updated successfully');
    }

    public function destroy(Income $income)
    {
        $income->delete();
        return redirect()->route('income.index')->with('success', 'Income deleted successfully');
    }
}