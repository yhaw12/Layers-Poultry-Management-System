<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\UserActivityLog;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('source', 'user')->pending();
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->start_date) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('date', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('date', 'desc')->paginate(10)->appends($request->query());
        return view('transactions.index', compact('transactions'));
    }

     public function approve(Request $request, Transaction $transaction)
    {
        try {
            $remainingAmount = $transaction->reference->amount - $transaction->reference->transactions()->where('status', 'approved')->sum('amount');

            if ($remainingAmount <= 0) {
                return back()->with('error', 'Transaction cannot be approved; payment already fulfilled.');
            }

            if ($request->amount && $request->amount < $transaction->amount) {
                // Handle partial payment
                $this->createTransaction($transaction->type, [
                    'amount' => $request->amount,
                    'status' => 'approved',
                    'description' => "Partial payment for {$transaction->description}",
                    'reference_id' => $transaction->reference_id,
                    'reference_type' => $transaction->reference_type,
                ]);
                $transaction->update(['amount' => $transaction->amount - $request->amount]);
            } else {
                $this->approveTransaction($transaction);
            }

            return redirect()->route('transactions.index')->with('success', 'Transaction approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve transaction.');
        }
    }

    public function reject(Transaction $transaction)
    {
        $transaction->updateStatus('rejected');

        Alert::create([
            'message' => "Transaction #{$transaction->id} ({$transaction->type}) rejected",
            'type' => 'transaction',
            'user_id' => Auth::id() ?? 1,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaction rejected successfully.');
    }
}

