<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Bird;
use App\Models\Egg;
use App\Models\Alert;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SalesController extends Controller
{
    /**
     * Display a listing of all sales.
     */
    public function __construct()
    {
        // Apply permission middleware to relevant methods
        // $this->middleware('permission:view-sales')->only(['index', 'sales', 'birdSales', 'invoices']);
        // $this->middleware('permission:edit-sales')->only(['create', 'store', 'edit', 'update']);
        // $this->middleware('permission:delete-sales')->only('destroy');
        // $this->middleware('permission:email-invoices')->only('emailInvoice');
        // $this->middleware('permission:update-invoice-status')->only('updateStatus');
    }
    public function index()
    {
        $sales = Sale::with('customer', 'saleable')
            ->orderBy('sale_date', 'desc')
            ->paginate(10);
        $totalSales = Sale::sum('total_amount') ?? 0;
        $totalQuantity = Sale::sum('quantity') ?? 0;

        return view('sales.index', compact('sales', 'totalSales', 'totalQuantity'));
    }

    /**
     * Display a listing of egg sales.
     */
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

    /**
     * Show the form for creating a new sale.
     */
    public function create()
    {
        $birds = Bird::all();
        $eggs = Egg::all();
        $customers = Customer::orderBy('name')->get();
        return view('sales.create', compact('birds', 'eggs', 'customers'));
    }

    /**
     * Store a newly created sale in storage.
     */
    public function store(StoreSaleRequest $request)
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

        // Sale::create($validated);
        Sale::create($request->validated());

        // Create alert with user_id
        Alert::create([
            'message' => "New sale for customer {$customer->name}",
            'type' => 'sale',
            'user_id' => Auth::id() ?? 1, // Fallback to user ID 1 if not authenticated
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    /**
     * Show the form for editing the specified sale.
     */
    public function edit(Sale $sale)
    {
        $birds = Bird::all();
        $eggs = Egg::all();
        $customers = Customer::orderBy('name')->get();
        return view('sales.edit', compact('sale', 'birds', 'eggs', 'customers'));
    }

    /**
     * Update the specified sale in storage.
     */
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

    /**
     * Remove the specified sale from storage.
     */
    public function destroy($id)
    {
        $sale = Sale::findorFail($id);
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }

    /**
     * Display a listing of bird sales.
     */
    public function birdSales()
    {
        $sales = Sale::with('customer', 'saleable')
            ->where('saleable_type', Bird::class)
            ->orderBy('sale_date', 'desc')
            ->paginate(10);
        $totalSales = Sale::where('saleable_type', Bird::class)->sum('total_amount') ?? 0;
        $totalQuantity = Sale::where('saleable_type', Bird::class)->sum('quantity') ?? 0;

        return view('sales.birds', compact('sales', 'totalSales', 'totalQuantity'));
    }


    // public function invoiceShow()
    // [
    //     return view('sales.invoice');
    // ]

    /**
     * Generate and download the invoice PDF for a sale.
     */
    public function invoice(Sale $sale)
    {
        $sale->load('customer', 'saleable');

        // Check if customer exists
        if (!$sale->customer) {
            return redirect()->back()->with('error', 'Customer not found for this sale.');
        }

        // Company information
        $company = [
            'name' => config('app.name'),
            'address' => 'Aprah Opeicuma, Awutu Senya West',
            'phone' => '0593036689',
            'email' => 'info@company.com',
        ];

        // Generate filename
        $customerName = Str::slug($sale->customer->name, '_');
        $saleDate = $sale->sale_date->format('Y-m-d');
        $filename = "invoice-{$customerName}-{$saleDate}.pdf";

        // Generate PDF
        $pdf = Pdf::loadView('sales.invoice', compact('sale', 'company'));

        // Download PDF
        return $pdf->download($filename);
    }



     public function invoices()
    {
        $sales = Sale::with('customer', 'saleable')
            ->orderBy('sale_date', 'desc')
            ->paginate(10);
        return view('invoices.index', compact('sales'));
    }




    public function updateStatus(Sale $sale)
    {
        $sale->update(['status' => 'paid']);
        Alert::create([
            'message' => "Invoice #{$sale->id} marked as paid for {$sale->customer->name}",
            'type' => 'payment',
            'user_id' => Auth::id() ?? 1,
        ]);
        return redirect()->route('invoices.index')->with('success', 'Invoice marked as paid.');
    }
}