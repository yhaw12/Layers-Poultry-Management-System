<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Transaction;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();
        if ($search = $request->input('search')) {
            $query->where('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $expenses = $query->orderBy('date', 'desc')->paginate(10);

        $expenseChart = Cache::remember('expense_trends', 3600, function () {
            $data = [];
            $labels = [];
            for ($i = 0; $i < 6; $i++) {
                $month = now()->subMonths($i);
                $labels[] = $month->format('M Y');
                $data[] = Expense::whereMonth('date', $month->month)
                    ->whereYear('date', $month->year)
                    ->sum('amount') ?? 0;
            }
            return ['data' => array_reverse($data), 'labels' => array_reverse($labels)];
        });

        $expenseLabels = $expenseChart['labels'];
        $expenseData = $expenseChart['data'];

        return view('expenses.index', compact('expenses', 'expenseLabels', 'expenseData'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:Structure,Feed,Veterinary,Utilities,Labor',
            'description' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $expense = Expense::create($validated);

        Transaction::create([
            'type' => 'expense',
            'amount' => $validated['amount'],
            'status' => 'pending',
            'date' => $validated['date'],
            'source_type' => Expense::class,
            'source_id' => $expense->id,
            'user_id' => auth()->id() ?? 1,
            'description' => "Expense for {$validated['category']}: {$validated['description']}",
        ]);

        UserActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'action' => 'created_expense',
            'details' => "Created expense of \${$validated['amount']} for {$validated['category']} on {$validated['date']}",
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'category' => 'required|in:Structure,Feed,Veterinary,Utilities,Labor',
            'description' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $expense->update($validated);

        Transaction::updateOrCreate(
            [
                'source_type' => Expense::class,
                'source_id' => $expense->id,
            ],
            [
                'type' => 'expense',
                'amount' => $validated['amount'],
                'status' =>  'pending',
                'date' => $validated['date'],
                'user_id' => auth()->id() ?? 1,
                'description' => "Updated expense for {$validated['category']}: {$validated['description']}",
            ]
        );

        UserActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'action' => 'updated_expense',
            'details' => "Updated expense of \${$validated['amount']} for {$validated['category']} on {$validated['date']}",
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        Transaction::where('source_type', Expense::class)
            ->where('source_id', $expense->id)
            ->delete();

        UserActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'action' => 'deleted_expense',
            'details' => "Deleted expense of \${$expense->amount} for {$expense->category} on {$expense->date}",
        ]);

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}