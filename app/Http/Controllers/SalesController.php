<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Bird;
use App\Models\Egg;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function sales()
       {
           $sales = Sale::with('customer', 'saleable')
               ->where('saleable_type', Egg::class)
               ->orderBy('sale_date', 'desc')
               ->paginate(10);
           $totalSales = Sale::where('saleable_type', Egg::class)->sum('total_amount') ?? 0;
           $totalCratesSold = Sale::where('saleable_type', Egg::class)->sum('quantity') ?? 0;

           return view('eggs.sales', compact('sales', 'totalSales', 'totalCratesSold'));
       }

    public function create()
    {
        $customers = Customer::all();
        $birds = Bird::all();
        $eggs = Egg::all(); // Adjust if eggs are tracked differently
        return view('sales.create', compact('customers', 'birds', 'eggs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'saleable_type' => 'required|in:App\Models\Bird,App\Models\Egg',
            'saleable_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
        ]);

        $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];
        Sale::create($validated);
        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function edit(Sale $sale)
    {
        $customers = Customer::all();
        $birds = Bird::all();
        $eggs = Egg::all();
        return view('sales.edit', compact('sale', 'customers', 'birds', 'eggs'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'saleable_type' => 'required|in:App\Models\Bird,App\Models\Egg',
            'saleable_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
        ]);

        $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];
        $sale->update($validated);
        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }

    public function birdSales()
    {
        $sales = Sale::with('customer', 'saleable')
                     ->where('saleable_type', Bird::class)
                     ->paginate(10);
        return view('sales.birds', compact('sales'));
    }
}