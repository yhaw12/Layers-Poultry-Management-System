<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer')->orderBy('created_at', 'desc')->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('orders.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,delivered,paid',
        ]);

        $order = Order::create($validated);

        Transaction::create([
            'type' => 'order',
            'amount' => $validated['total_amount'],
            'status' => $validated['status'],
            'date' => now(),
            'source_type' => Order::class,
            'source_id' => $order->id,
            'user_id' => Auth::id() ?? 1,
            'description' => "Order #{$order->id} for customer ID {$validated['customer_id']}",
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'created_order',
            'details' => "Created order #{$order->id} for customer ID {$validated['customer_id']} with total \${$validated['total_amount']}",
        ]);

        return redirect()->route('orders.index')->with('success', 'Order created.');
    }

    public function edit(Order $order)
    {
        $customers = Customer::all();
        return view('orders.edit', compact('order', 'customers'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,delivered,paid',
        ]);

        $order->update($validated);

        Transaction::updateOrCreate(
            [
                'source_type' => Order::class,
                'source_id' => $order->id,
            ],
            [
                'type' => 'order',
                'amount' => $validated['total_amount'],
                'status' => $validated['status'],
                'date' => now(),
                'user_id' => Auth::id() ?? 1,
                'description' => "Updated order #{$order->id} for customer ID {$validated['customer_id']}",
            ]
        );

        UserActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'updated_order',
            'details' => "Updated order #{$order->id} for customer ID {$validated['customer_id']} with total \${$validated['total_amount']}",
        ]);

        return redirect()->route('orders.index')->with('success', 'Order updated successfully');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        Transaction::where('source_type', Order::class)
            ->where('source_id', $order->id)
            ->delete();

        UserActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'deleted_order',
            'details' => "Deleted order #{$order->id} for customer ID {$order->customer_id}",
        ]);

        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully');
    }
}