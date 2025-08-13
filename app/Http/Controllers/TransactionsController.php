<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\UserActivityLog;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'nullable|date|before_or_equal:end_date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());
            $cacheKey = "transactions_{$start}_{$end}";

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
        } catch (\Exception $e) {
            Log::error('Failed to load transactions', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load transactions.');
        }
    }

    public function approve(Request $request, Transaction $transaction)
    {
        try {
            if ($transaction->status !== 'pending') {
                return back()->with('error', 'Transaction is not in a pending state.');
            }

            $remainingAmount = $transaction->reference->amount - $transaction->reference->transactions()->where('status', 'approved')->whereNull('deleted_at')->sum('amount');

            if ($remainingAmount <= 0) {
                return back()->with('error', 'Transaction cannot be approved; payment already fulfilled.');
            }

            if ($request->amount && $request->amount < $transaction->amount) {
                Transaction::create([
                    'type' => $transaction->type,
                    'amount' => $request->amount,
                    'status' => 'approved',
                    'description' => "Partial payment for {$transaction->description}",
                    'reference_id' => $transaction->reference_id,
                    'reference_type' => $transaction->reference_type,
                    'user_id' => Auth::id() ?? 1,
                    'date' => now(),
                ]);
                $transaction->update(['amount' => $transaction->amount - $request->amount]);
            } else {
                $transaction->update(['status' => 'approved']);
            }

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'approved_transaction',
                'details' => "Approved transaction #{$transaction->id} (Type: {$transaction->type})",
            ]);

            return redirect()->route('transactions.index')->with('success', 'Transaction approved successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to approve transaction', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to approve transaction.');
        }
    }

    public function reject(Transaction $transaction)
    {
        try {
            if ($transaction->status !== 'pending') {
                return back()->with('error', 'Transaction is not in a pending state.');
            }

            $transaction->update(['status' => 'rejected']);

            Alert::create([
                'message' => "Transaction #{$transaction->id} ({$transaction->type}) rejected",
                'type' => 'transaction',
                'user_id' => Auth::id() ?? 1,
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'rejected_transaction',
                'details' => "Rejected transaction #{$transaction->id} (Type: {$transaction->type})",
            ]);

            return redirect()->route('transactions.index')->with('success', 'Transaction rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to reject transaction', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to reject transaction.');
        }
    }
}