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

    public function approve(Transaction $transaction)
    {
        $transaction->updateStatus('approved');

        // Update source status if applicable
        if ($transaction->source_type === \App\Models\Sale::class) {
            $transaction->source->update(['status' => 'paid']);
        } elseif ($transaction->source_type === \App\Models\Order::class) {
            $transaction->source->update(['status' => 'paid']);
        }

        Alert::create([
            'message' => "Transaction #{$transaction->id} ({$transaction->type}) approved",
            'type' => 'transaction',
            'user_id' => Auth::id() ?? 1,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaction approved successfully.');
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