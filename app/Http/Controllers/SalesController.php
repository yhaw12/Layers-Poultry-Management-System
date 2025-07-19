<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Bird;
use App\Models\Egg;
use App\Models\Alert;
use App\Models\UserActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\InvoiceMail;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-sales')->only(['index', 'sales', 'birdSales', 'invoices']);
        $this->middleware('permission:edit-sales')->only(['create', 'store', 'edit', 'update']);
        $this->middleware('permission:delete-sales')->only('destroy');
        $this->middleware('permission:email-invoices')->only('emailInvoice');
        $this->middleware('permission:update-invoice-status')->only('updateStatus');
    }

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

    /**
     * Show the form for creating a new sale.
     */
    public function create()
    {
        $birds = Bird::where('quantity', '>', 0)->get();
        $eggs = Egg::where('crates', '>', 0)->get();
        $customers = Customer::orderBy('name')->get();
        return view('sales.create', compact('birds', 'eggs', 'customers'));
    }

    /**
     * Store a newly created sale in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        $validated = $request->validated();

        // Validate stock availability
        if ($validated['saleable_type'] === 'App\Models\Bird') {
            $bird = Bird::find($validated['saleable_id']);
            if (!$bird || $bird->quantity < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'Insufficient bird stock available.']);
            }
        } else {
            $egg = Egg::find($validated['saleable_id']);
            if (!$egg || $egg->crates < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'Insufficient egg crates available.']);
            }
        }

        // Handle customer creation or retrieval
        $customer = Customer::firstOrCreate(
            ['name' => $validated['customer_name']],
            ['phone' => $validated['customer_phone'] ?? '']
        );

        $validated['customer_id'] = $customer->id;
        $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];

        $sale = Sale::create($validated);

        // Update stock
        if ($validated['saleable_type'] === 'App\Models\Bird') {
            $bird->decrement('quantity', $validated['quantity']);
        } else {
            $egg->decrement('crates', $validated['quantity']);
        }

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'created_sale',
            'details' => "Created sale #{$sale->id} for {$customer->name} (Total: {$validated['total_amount']})",
        ]);

        // Create alert
        Alert::create([
            'message' => "New sale #{$sale->id} for customer {$customer->name}",
            'type' => 'sale',
            'user_id' => Auth::id() ?? 1,
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    /**
     * Show the form for editing the specified sale.
     */
    public function edit(Sale $sale)
    {
        $birds = Bird::where('quantity', '>', 0)->get();
        $eggs = Egg::where('crates', '>', 0)->get();
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

        // Validate stock availability (accounting for original quantity)
        $quantityDiff = $validated['quantity'] - $sale->quantity;
        if ($quantityDiff > 0) {
            if ($validated['saleable_type'] === 'App\Models\Bird') {
                $bird = Bird::find($validated['saleable_id']);
                if (!$bird || $bird->quantity < $quantityDiff) {
                    return back()->withErrors(['quantity' => 'Insufficient bird stock available.']);
                }
            } else {
                $egg = Egg::find($validated['saleable_id']);
                if (!$egg || $egg->crates < $quantityDiff) {
                    return back()->withErrors(['quantity' => 'Insufficient egg crates available.']);
                }
            }
        }

        // Handle customer creation or retrieval
        $customer = Customer::firstOrCreate(
            ['name' => $validated['customer_name']],
            ['phone' => $validated['customer_phone'] ?? '']
        );

        $validated['customer_id'] = $customer->id;
        $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];

        // Update stock
        if ($quantityDiff != 0) {
            if ($sale->saleable_type === 'App\Models\Bird') {
                $originalBird = Bird::find($sale->saleable_id);
                if ($originalBird) {
                    $originalBird->increment('quantity', $sale->quantity);
                }
            } else {
                $originalEgg = Egg::find($sale->saleable_id);
                if ($originalEgg) {
                    $originalEgg->increment('crates', $sale->quantity);
                }
            }

            if ($validated['saleable_type'] === 'App\Models\Bird') {
                $bird = Bird::find($validated['saleable_id']);
                if ($bird) {
                    $bird->decrement('quantity', $validated['quantity']);
                }
            } else {
                $egg = Egg::find($validated['saleable_id']);
                if ($egg) {
                    $egg->decrement('crates', $validated['quantity']);
                }
            }
        }

        $sale->update($validated);

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'updated_sale',
            'details' => "Updated sale #{$sale->id} for {$customer->name} (Total: {$validated['total_amount']})",
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    /**
     * Remove the specified sale from storage.
     */
    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);

        // Restore stock
        if ($sale->saleable_type === 'App\Models\Bird') {
            $bird = Bird::find($sale->saleable_id);
            if ($bird) {
                $bird->increment('quantity', $sale->quantity);
            }
        } else {
            $egg = Egg::find($sale->saleable_id);
            if ($egg) {
                $egg->increment('crates', $sale->quantity);
            }
        }

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'deleted_sale',
            'details' => "Deleted sale #{$sale->id} for {$sale->customer->name} (Total: {$sale->total_amount})",
        ]);

        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }

    /**
     * Display the invoices listing.
     */
    public function invoices()
    {
        $sales = Sale::with('customer', 'saleable')
            ->orderBy('sale_date', 'desc')
            ->paginate(10);
        return view('invoices.index', compact('sales'));
    }

    /**
     * Generate and preview/download the invoice PDF for a sale.
     */
    public function invoice(Sale $sale, Request $request)
    {
        $sale->load('customer', 'saleable');

        if (!$sale->customer || !$sale->saleable) {
            return redirect()->back()->with('error', 'Customer or product not found for this sale.');
        }

        $company = [
            'name' => config('app.name'),
            'address' => 'Aprah Opeicuma, Awutu Senya West',
            'phone' => '0593036689',
            'email' => 'info@company.com',
        ];

        $customerName = Str::slug($sale->customer->name, '_');
        $saleDate = $sale->sale_date->format('Y-m-d');
        $filename = "invoice-{$customerName}-{$saleDate}.pdf";

        $pdf = Pdf::loadView('sales.invoice', compact('sale', 'company'));

        if ($request->query('preview', 0)) {
            return $pdf->stream($filename);
        }

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'generated_invoice',
            'details' => "Generated invoice for sale #{$sale->id} for {$sale->customer->name}",
        ]);

        return $pdf->download($filename);
    }

    /**
     * Update the invoice status (e.g., mark as paid).
     */
    public function updateStatus(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,overdue',
        ]);

        $oldStatus = $sale->status;
        $sale->update(['status' => $validated['status']]);

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'updated_invoice_status',
            'details' => "Changed status of invoice #{$sale->id} for {$sale->customer->name} from {$oldStatus} to {$validated['status']}",
        ]);

        // Create alert
        Alert::create([
            'message' => "Invoice #{$sale->id} status changed to {$validated['status']} for {$sale->customer->name}",
            'type' => 'payment',
            'user_id' => Auth::id() ?? 1,
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice status updated successfully.');
    }

    /**
     * Email the invoice to the customer.
     */
    // public function emailInvoice(Sale $sale)
    // {
    //     $sale->load('customer', 'saleable');

    //     if (!$sale->customer || !$sale->customer->email) {
    //         return redirect()->back()->with('error', 'Customer email not found.');
    //     }

    //     $company = [
    //         'name' => config('app.name'),
    //         'address' => 'Aprah Opeicuma, Awutu Senya West',
    //         'phone' => '0593036689',
    //         'email' => 'info@company.com',
    //     ];

    //     $pdf = Pdf::loadView('sales.invoice', compact('sale', 'company'));
    //     $pdfData = $pdf->output();

    //     Mail::to($sale->customer->email)->send(new InvoiceMail($sale, $company, $pdfData));

    //     // Log activity
    //     UserActivityLog::create([
    //         'user_id' => Auth::id() ?? 1,
    //         'action' => 'emailed_invoice',
    //         'details' => "Emailed invoice #{$sale->id} to {$sale->customer->name}",
    //     ]);

    //     return redirect()->route('invoices.index')->with('success', 'Invoice emailed successfully.');
    // }
}