<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Bird;
use App\Models\Egg;
use App\Models\Alert;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;


class SalesController extends Controller
{
    /**
     * Display a listing of all sales.
     */
    public function index()
    {
        $sales = Sale::with('customer', 'saleable')
            ->orderBy('sale_date', 'desc')
            ->paginate(10);
        $totalSales = Sale::sum('total_amount') ?? 0;
        $totalQuantity = Sale::sum('quantity') ?? 0;

        return view('sales.index', compact('sales', 'totalSales', 'totalQuantity'));
    }

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
        $birds = Bird::all();
        $eggs = Egg::all();
        $customers = Customer::orderBy('name')->get(); // Ensure customers are fetched
        return view('sales.create', compact('birds', 'eggs', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'saleable_type' => 'required|in:App\Models\Bird,App\Models\Egg',
            'saleable_id' => 'required|integer',
            'product_variant' => 'required|in:big,small,cracked,broiler,layer',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
        ]);

        // Handle customer creation or retrieval
        $customer = Customer::firstOrCreate(
            ['name' => $validated['customer_name']],
            ['phone' => $validated['customer_phone'] ?? '']
        );

        $validated['customer_id'] = $customer->id;
        $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];

        // Validate product_variant against saleable_type
        if ($validated['saleable_type'] === 'App\Models\Bird' && !in_array($validated['product_variant'], ['broiler', 'layer'])) {
            return back()->withErrors(['product_variant' => 'Product variant must be broiler or layer for bird sales.']);
        }
        if ($validated['saleable_type'] === 'App\Models\Egg' && !in_array($validated['product_variant'], ['big', 'small', 'cracked'])) {
            return back()->withErrors(['product_variant' => 'Product variant must be big, small, or cracked for egg sales.']);
        }

        Sale::create($validated);

        Alert::create([
            'message' => "New sale for customer {$customer->name}",
            'type' => 'sale',
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function edit(Sale $sale)
    {
        $birds = Bird::all();
        $eggs = Egg::all();
        $customers = Customer::orderBy('name')->get();
        return view('sales.edit', compact('sale', 'birds', 'eggs', 'customers'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'saleable_type' => 'required|in:App\Models\Bird,App\Models\Egg',
            'saleable_id' => 'required|integer',
            'product_variant' => 'required|in:big,small,cracked,broiler,layer',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
        ]);

        // Handle customer creation or retrieval
        $customer = Customer::firstOrCreate(
            ['name' => $validated['customer_name']],
            ['phone' => $validated['customer_phone'] ?? '']
        );

        $validated['customer_id'] = $customer->id;
        $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];

        // Validate product_variant against saleable_type
        if ($validated['saleable_type'] === 'App\Models\Bird' && !in_array($validated['product_variant'], ['broiler', 'layer'])) {
            return back()->withErrors(['product_variant' => 'Product variant must be broiler or layer for bird sales.']);
        }
        if ($validated['saleable_type'] === 'App\Models\Egg' && !in_array($validated['product_variant'], ['big', 'small', 'cracked'])) {
            return back()->withErrors(['product_variant' => 'Product variant must be big, small, or cracked for egg sales.']);
        }

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

    public function invoice(Sale $sale)
{
    // Load related data (e.g., customer and saleable item details)
    $sale->load('customer', 'saleable');
    
    // Generate the PDF from a Blade view
    $pdf = Pdf::loadView('sales.invoice', compact('sale'));
    
    // Trigger the download with a filename based on the sale ID
    return $pdf->download("invoice-{$sale->id}.pdf");
}
}