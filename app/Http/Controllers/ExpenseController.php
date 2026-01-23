<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Expense;
use App\Models\Feed;
use App\Models\MedicineLog;
use App\Models\Payroll;
use App\Models\Transaction;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $expenseChart = Cache::remember('expense_trends', 3, function () {
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
            'category'    => 'required|in:Structure,Feed,MedicineLog,Utilities,Labor',
            'description' => 'nullable|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|date',
        ]);

        return DB::transaction(function () use ($validated) {
            $expense = Expense::create($validated);

            // Sync to Feed Inventory if category is Feed
            if ($validated['category'] === 'Feed') {
                $this->syncToFeed($expense);
            } elseif ($validated['category'] === 'MedicineLog') {
                $this->syncToMedicine($expense);
            }elseif ($validated['category'] === 'Labor') {
                $this->syncToPayroll($expense);
            }

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

            return redirect()->route('expenses.index')->with('success', 'Expense and Feed record added.');
        });
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
            'category'    => 'required|in:Structure,Feed,MedicineLog,Utilities,Labor',
            'description' => 'nullable|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|date',
        ]);

        return DB::transaction(function () use ($validated, $expense) {
            $oldCategory = $expense->category;
            $expense->update($validated);

            // --- SYNC LOGIC START ---
            
            // Clean up previous syncs if category changed
            if ($oldCategory !== $validated['category']) {
                if ($oldCategory === 'Feed') Feed::where('expense_id', $expense->id)->delete();
                if ($oldCategory === 'MedicineLog') MedicineLog::where('expense_id', $expense->id)->delete();
                if ($oldCategory === 'Labor') Payroll::where('expense_id', $expense->id)->delete();
            }

            // Apply new sync
            if ($validated['category'] === 'Feed') {
                $this->syncToFeed($expense);
            } elseif ($validated['category'] === 'MedicineLog') {
                $this->syncToMedicine($expense);
            } elseif ($validated['category'] === 'Labor') {
                $this->syncToPayroll($expense);
            }

            Transaction::updateOrCreate(
                ['source_type' => Expense::class, 'source_id' => $expense->id],
                [
                    'type' => 'expense',
                    'amount' => $validated['amount'],
                    'status' => 'pending',
                    'date' => $validated['date'],
                    'user_id' => auth()->id() ?? 1,
                    'description' => "Updated expense for {$validated['category']}: {$validated['description']}",
                ]
            );

            return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
        });
    }

     public function destroy(Request $request, $id)
    {
        try {
            $expense = Expense::findOrFail($id);
            
            // Feed record is deleted automatically by the database cascade
            $expense->delete();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Expense and Inventory updated.']);
            }
            return redirect()->route('expenses.index')->with('success', 'Deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('expenses.index')->with('error', 'Failed to delete.');
        }
    }
    /**
     * Helper to sync Expense to Feed Table
     */
    /**
 * Helper to sync Expense to Feed Table using the new expense_id column
 */
    private function syncToFeed(Expense $expense)
    {
        // Clean up the description
        $desc = trim($expense->description);
        
        // Split description into parts (e.g., "Grower Mash 10")
        $parts = explode(' ', $desc);
        
        $quantity = 1; // Default quantity as requested
        $feedType = $desc;

        // Check if the last part is a number (e.g., the "10" in "Grower 10")
        if (count($parts) > 1) {
            $lastPart = end($parts);
            if (is_numeric($lastPart)) {
                $quantity = (float) $lastPart; // Capture the number as quantity
                array_pop($parts);             // Remove the number from the name
                $feedType = implode(' ', $parts); // Join the rest as the type
            }
        }

        // Update the feed record linked to this expense, or create it if it doesn't exist
        Feed::updateOrCreate(
            ['expense_id' => $expense->id], 
            [
                'type'          => $feedType,
                'quantity'      => $quantity,
                'purchase_date' => $expense->date,
                'cost'          => $expense->amount,
                'weight'        => 0, // Placeholder
                'synced_at'     => now(),
            ]
        );
    }


    /**
     * Helper to sync Expense to MedicineLog Table
     */
    private function syncToMedicine(Expense $expense)
    {
        // Parsing Logic: "Antibiotics 5" -> Name: Antibiotics, Qty: 5
        $desc = trim($expense->description);
        $parts = explode(' ', $desc);
        
        $quantity = 1; // Default
        $name = $desc;

        if (count($parts) > 1) {
            $lastPart = end($parts);
            if (is_numeric($lastPart)) {
                $quantity = (float) $lastPart;
                array_pop($parts);
                $name = implode(' ', $parts);
            }
        }

        MedicineLog::updateOrCreate(
            ['expense_id' => $expense->id],
            [
                'medicine_name' => $name,
                'type'          => 'purchase', // Expenses are always purchases
                'quantity'      => $quantity,
                'unit'          => 'units',    // Default unit since expense description doesn't specify
                'date'          => $expense->date,
                'notes'         => 'Synced from Expense Record',
            ]
        );
    }


    /**
     * Helper to sync Expense to Payroll Table
     * Looks for Employee name in description.
     */
    private function syncToPayroll(Expense $expense)
    {
        $description = strtolower($expense->description);
        $employees = Employee::whereNull('deleted_at')->get();
        $matchedEmployee = null;

        // MATCHING LOGIC
        foreach ($employees as $employee) {
            $empName = strtolower($employee->name);
            
            // 1. Check Exact Full Name Match (Best)
            if (str_contains($description, $empName)) {
                $matchedEmployee = $employee;
                break; 
            }

            // 2. Check First Name or Last Name (Whole Word Match)
            $nameParts = explode(' ', $empName);
            foreach ($nameParts as $part) {
                if (strlen($part) > 2) { // Ignore short initials like "A."
                    // Ensure it matches "John" but not "Johnson" using boundaries
                    if (preg_match("/\b" . preg_quote($part, '/') . "\b/", $description)) {
                        $matchedEmployee = $employee;
                        break 2; // Break both loops
                    }
                }
            }

            // 3. Check for at least 4 continuous letters (Fuzzy Match)
            // Only if we haven't found a match yet
            if (strlen($empName) >= 4) {
                // Check if any 4-char chunk of the employee name exists in the description
                // We check chunks to avoid false positives on very short overlaps
                $chunks = str_split($empName, 4);
                if (count($chunks) > 0 && str_contains($description, $chunks[0])) {
                     $matchedEmployee = $employee;
                     break;
                }
            }
        }

        // If we found an employee, create/update the Payroll record
        if ($matchedEmployee) {
            Payroll::updateOrCreate(
                ['expense_id' => $expense->id],
                [
                    'employee_id' => $matchedEmployee->id,
                    'pay_date'    => $expense->date,
                    // We assume the Expense Amount is the final Net Pay they received
                    'net_pay'     => $expense->amount, 
                    'base_salary' => $matchedEmployee->monthly_salary, // Keep original salary record
                    'bonus'       => 0,
                    'deductions'  => 0,
                    'status'      => 'paid', // Since it's an expense, it's already paid
                    'notes'       => 'Synced from Expense: ' . $expense->description,
                ]
            );
        }
    }
}