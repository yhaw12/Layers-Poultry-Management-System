<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\UserActivityLog;
use App\Models\Alert;
use App\Models\Income;
use App\Models\Order;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'nullable|date|before_or_equal:end_date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'type' => 'nullable|in:sale,expense,income,order',
            ]);

            $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());
            $cacheKey = "transactions_{$request->type}_{$start}_{$end}";

            $query = Transaction::with('source', 'user')
                ->where('status', 'pending')
                ->whereNull('deleted_at');

            if ($request->type) {
                $query->where('type', $request->type);
            }
            if ($request->start_date) {
                $query->where('date', '>=', $request->start_date);
            }
            if ($request->end_date) {
                $query->where('date', '<=', $request->end_date);
            }

            $transactions = Cache::remember($cacheKey, 300, function () use ($query) {
                return $query->orderBy('date', 'desc')->paginate(10);
            });

            return view('transactions.index', compact('transactions', 'start', 'end'));
        } catch (ValidationException $e) {
            Log::warning('Validation failed for transactions index', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to load transactions', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load transactions.');
        }
    }

    public function approve(Request $request, $id)
{
    DB::beginTransaction();
    try {
        $transaction = Transaction::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'amount' => 'nullable|numeric|min:0|max:' . $transaction->amount,
            'transaction_id' => 'required|exists:transactions,id',
        ]);

        if ($validator->fails()) {
            DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        if ($transaction->status !== 'pending') {
            DB::rollBack();
            $msg = 'Transaction is not in a pending state.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        // Note: we deliberately DO NOT update related Sale/Order/etc here.
        // We only update the transaction(s) themselves to reflect approval.
        if ($request->filled('amount') && (float)$request->amount < (float)$transaction->amount) {
            // Create an approved transaction for the partial amount
            Transaction::create([
                'type' => $transaction->type,
                'amount' => $request->amount,
                'status' => 'approved',
                'description' => 'Partial approval for: ' . ($transaction->description ?? ''),
                'reference_id' => $transaction->reference_id ?? null,
                'reference_type' => $transaction->reference_type ?? null,
                'user_id' => Auth::id() ?? 1,
                'date' => now(),
            ]);
            // Reduce the original pending transaction amount
            $transaction->amount = $transaction->amount - $request->amount;
            $transaction->save();

            $message = 'Partial transaction approved successfully.';
        } else {
            // Approve the whole thing
            $transaction->update(['status' => 'approved']);
            $message = 'Transaction approved successfully.';
        }

        // Logs / alerts (optional)
        UserActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'approved_transaction',
            'details' => "Approved transaction #{$transaction->id} (Type: {$transaction->type})",
        ]);
        Alert::create([
            'message' => "Transaction #{$transaction->id} ({$transaction->type}) approved",
            'type' => 'transaction',
            'user_id' => Auth::id() ?? 1,
        ]);

        DB::commit();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => $message, 'status' => 'ok'], 200);
        }

        return redirect()->route('transactions.index')->with('success', $message);
    } catch (ValidationException $e) {
        DB::rollBack();
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to approve transaction', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        $msg = 'Failed to approve transaction.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => $msg], 500);
        }
        return back()->with('error', $msg);
    }
}

   public function reject(Request $request, $id)
{
    try {
        $transaction = Transaction::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:255',
            'transaction_id' => 'required|exists:transactions,id',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        if ($transaction->status !== 'pending') {
            $msg = 'Transaction is not in a pending state.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $transaction->update(['status' => 'rejected']);

        // Create an alert & log (safe — no sale changes)
        Alert::create([
            'message' => "Transaction #{$transaction->id} ({$transaction->type}) rejected: {$request->reason}",
            'type' => 'transaction',
            'user_id' => Auth::id() ?? 1,
        ]);
        UserActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'rejected_transaction',
            'details' => "Rejected transaction #{$transaction->id} (Type: {$transaction->type}) with reason: {$request->reason}",
        ]);

        $msg = 'Transaction rejected successfully.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => $msg, 'status' => 'ok'], 200);
        }
        return redirect()->route('transactions.index')->with('success', $msg);
    } catch (ValidationException $e) {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        Log::error('Failed to reject transaction', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        $msg = 'Failed to reject transaction.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => $msg], 500);
        }
        return back()->with('error', $msg);
    }
}

    public function export(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'nullable|date|before_or_equal:end_date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'type' => 'nullable|in:sale,expense,income,order',
            ]);

            $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());

            $query = Transaction::with('source', 'user')
                ->where('status', 'pending')
                ->whereNull('deleted_at');

            if ($request->type) {
                $query->where('type', $request->type);
            }
            if ($request->start_date) {
                $query->where('date', '>=', $request->start_date);
            }
            if ($request->end_date) {
                $query->where('date', '<=', $request->end_date);
            }

            $transactions = $query->orderBy('date', 'desc')->get();

            $data = [
                ['ID', 'Type', 'Amount', 'Date', 'Source', 'Status', 'Created By'],
            ];

            foreach ($transactions as $transaction) {
                $source = 'N/A';
                if ($transaction->source_type === \App\Models\Sale::class && $transaction->source) {
                    $source = "Sale #{$transaction->source_id}";
                } elseif ($transaction->source_type === \App\Models\Expense::class && $transaction->source) {
                    $source = "Expense: {$transaction->source->category}";
                } elseif ($transaction->source_type === \App\Models\Income::class && $transaction->source) {
                    $source = "Income: {$transaction->source->source}";
                } elseif ($transaction->source_type === \App\Models\Order::class && $transaction->source) {
                    $source = "Order #{$transaction->source_id}";
                }

                $data[] = [
                    $transaction->id,
                    ucfirst($transaction->type),
                    number_format($transaction->amount, 2),
                    $transaction->date->format('Y-m-d'),
                    $source,
                    ucfirst($transaction->status),
                    $transaction->user->name ?? 'N/A',
                ];
            }

            $filename = "transactions_export_" . now()->format('Ymd_His') . ".csv";
            $handle = fopen('php://output', 'w');
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            fputs($handle, "\xEF\xBB\xBF"); // Add UTF-8 BOM for Excel compatibility
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
            exit;
        } catch (ValidationException $e) {
            Log::warning('Validation failed for transactions export', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to export transactions', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to export transactions.');
        }
    }

    public function show($id)
    {
        try {
            $transaction = Transaction::with(['source', 'user', 'customer'])->findOrFail($id);

            return view('transactions.show', compact('transaction'));
        } catch (\Exception $e) {
            Log::error('Failed to load transaction details', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $id
            ]);
            return back()->with('error', 'Failed to load transaction details.');
        }
    }
}


