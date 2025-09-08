<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Transaction;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IncomeController extends Controller
{
    public function index()
    {
        try {
            $income = Income::withoutTrashed()->get();
            $incomeData = [];
            $incomeLabels = [];
            for ($i = 0; $i < 6; $i++) {
                $month = now()->subMonths($i);
                $incomeLabels[] = $month->format('M Y');
                $incomeData[] = Income::withoutTrashed()
                    ->whereMonth('date', $month->month)
                    ->whereYear('date', $month->year)
                    ->sum('amount') ?? 0;
            }
            $incomeLabels = array_reverse($incomeLabels);
            $incomeData = array_reverse($incomeData);

            return view('income.index', compact('income', 'incomeLabels', 'incomeData'));
        } catch (\Exception $e) {
            Log::error('Failed to load income index', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to load income records. Please try again.');
        }
    }

    public function create()
    {
        return view('income.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'source' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'date' => 'required|date',
            ]);

            $data['created_by'] = Auth::id() ?? 1;
            $income = Income::create($data);

            Transaction::create([
                'type' => 'income',
                'amount' => $data['amount'],
                'status' => 'pending',
                'date' => $data['date'],
                'source_type' => Income::class,
                'source_id' => $income->id,
                'user_id' => Auth::id() ?? 1,
                'description' => "Income from {$data['source']}: {$data['description']}",
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'created_income',
                'details' => "Created income of ₵{$data['amount']} from {$data['source']} on {$data['date']}",
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Income added successfully.'
                ], 200);
            }

            return redirect()->route('income.index')->with('success', 'Income added successfully');
        } catch (\Exception $e) {
            Log::error('Failed to store income', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'input' => $request->all()]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add income record: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to add income record: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Income $income)
    {
        return view('income.edit', compact('income'));
    }

    public function update(Request $request, Income $income)
    {
        try {
            $data = $request->validate([
                'source' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'date' => 'required|date',
            ]);

            $data['created_by'] = Auth::id() ?? 1;
            $income->update($data);

            Transaction::updateOrCreate(
                [
                    'source_type' => Income::class,
                    'source_id' => $income->id,
                ],
                [
                    'type' => 'income',
                    'amount' => $data['amount'],
                    'status' => 'pending',
                    'date' => $data['date'],
                    'user_id' => Auth::id() ?? 1,
                    'description' => "Updated income from {$data['source']}: {$data['description']}",
                ]
            );

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'updated_income',
                'details' => "Updated income of ₵{$data['amount']} from {$data['source']} on {$data['date']}",
            ]);

            return redirect()->route('income.index')->with('success', 'Income updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update income', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'input' => $request->all()]);
            return redirect()->back()->with('error', 'Failed to update income record: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $income = Income::findOrFail($id);

            Transaction::where('source_type', Income::class)
                ->where('source_id', $income->id)
                ->delete();

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'deleted_income',
                'details' => "Deleted income of ₵{$income->amount} from {$income->source} on {$income->date}",
            ]);

            $income->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Income record deleted successfully.'
                ], 200);
            }

            return redirect()->route('income.index')->with('success', 'Income record deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete income', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete income record: ' . ($e->getCode() == 23000 ? 'This income is linked to other data.' : $e->getMessage())
                ], 500);
            }
            return redirect()->route('income.index')->with('error', 'Failed to delete income record: ' . $e->getMessage());
        }
    }
}