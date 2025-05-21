<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller {
    public function index() {
        $expenses = Expense::all();
         // Prepare chart data for expenses
         $currentMonth = now()->month;
         $currentYear = now()->year;
         $previousMonth = now()->subMonth()->month;
         $previousYear = now()->subMonth()->year;
 
         $currentExpenses = Expense::whereMonth('date', $currentMonth)
                                   ->whereYear('date', $currentYear)
                                   ->sum('amount');
         $previousExpenses = Expense::whereMonth('date', $previousMonth)
                                    ->whereYear('date', $previousYear)
                                    ->sum('amount');
 
         $expenseLabels = ['Previous Month', 'Current Month'];
         $expenseData = [$previousExpenses, $currentExpenses];
 
         return view('expenses.index', compact('expenses', 'expenseLabels', 'expenseData'));
     
    }

    public function create() {
        return view('expenses.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'category' => 'required|string',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);
        Expense::create($validated);
        return redirect()->route('expenses.index')->with('success', 'Expense added!');
    }

    public function show(Expense $expense) {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense) {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense) {
        $validated = $request->validate([
            'category' => 'required|string',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);
        $expense->update($validated);
        return redirect()->route('expenses.index')->with('success', 'Expense updated!');
    }

    public function destroy(Expense $expense) {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted!');
    }
}