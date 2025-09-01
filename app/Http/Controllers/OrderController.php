<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
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
            $cacheKey = "orders_{$start}_{$end}";

            $orders = Cache::remember($cacheKey, 300, function () use ($start, $end) {
                return Order::with('customer')
                    ->whereBetween('created_at', [$start, $end])
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
            });

            // $totalAmount = Order::whereBetween('created_at', [$start, $end])
            //                ->whereNull('deleted_at')
            //                ->sum('total_amount');
        // $totalPaid = Order::whereBetween('created_at', [$start, $end])
        //                  ->whereNull('deleted_at')
        //                  ->sum('paid_amount');

            return view('orders.index', compact('orders', 'start', 'end'));
        } catch (\Exception $e) {
            Log::error('Failed to load orders', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load orders.');
        }
    }

    public function create()
    {
        $customers = Customer::whereNull('deleted_at')->get();
        return view('orders.create', compact('customers'));
    }

    public function store(Request $request)
    {
        try {
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
                'date' => $order->created_at,
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
        } catch (\Exception $e) {
            Log::error('Failed to store order', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to create order.');
        }
    }

    public function edit(Order $order)
    {
        $customers = Customer::whereNull('deleted_at')->get();
        return view('orders.edit', compact('order', 'customers'));
    }

    public function update(Request $request, Order $order)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Failed to update order', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to update order.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $order = Order::whereNull('deleted_at')->findOrFail($id);

            Transaction::where('source_type', Order::class)
                ->where('source_id', $order->id)
                ->delete();

            UserActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'action' => 'deleted_order',
            'details' => "Deleted order #{$order->id} with total amount ₵{$order->total_amount}" . ($order->customer ? " for customer {$order->customer->name}" : ""),
        ]);

        // Delete the order (soft delete if enabled)
        $order->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully.'
            ], 200);
        }

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    } catch (\Exception $e) {
        Log::error('Failed to delete order: ' . $e->getMessage());

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order. ' . ($e->getCode() == 23000 ? 'This order is linked to other data.' : 'Please try again.')
            ], 500);
        }

        return redirect()->route('orders.index')->with('error', 'Failed to delete order.');
        }
    }
}