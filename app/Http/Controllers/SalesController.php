<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Bird;
use App\Models\Egg;
use App\Models\Alert;
use App\Models\Transaction;
use App\Models\UserActivityLog;
use App\Models\Income;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SalesController extends Controller
{
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

    public function invoices(Request $request)
    {
        $query = Sale::with('customer');
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->start_date) {
            $query->where('sale_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('sale_date', '<=', $request->end_date);
        }
        $sales = $query->orderBy('sale_date', 'desc')->paginate(10)->appends($request->query());
        return view('invoices.index', compact('sales'));
    }

    public function create()
    {
        $birds = Bird::where('quantity', '>', 0)->get();
        $eggs = Egg::where('crates', '>', 0)->get();
        $customers = Customer::orderBy('name')->get();
        return view('sales.create', compact('birds', 'eggs', 'customers'));
    }

    public function store(StoreSaleRequest $request)
    {
        if (!Auth::check()) {
            return back()->withErrors(['auth' => 'User must be authenticated to record a sale.']);
        }

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
            ['phone' => $validated['customer_phone'] ?? '', 'email' => $validated['customer_email'] ?? '']
        );

        // Calculate total and set default due date
        $validated['customer_id'] = $customer->id;
        $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];
        $validated['due_date'] = $validated['due_date'] ?? Carbon::parse($validated['sale_date'])->addDays(7);
        $validated['paid_amount'] = 0;
        $validated['status'] = 'pending';

        $sale = Sale::create($validated);

        // Update stock
        if ($validated['saleable_type'] === 'App\Models\Bird') {
            $bird->decrement('quantity', $validated['quantity']);
        } else {
            $egg->decrement('crates', $validated['quantity']);
        }

        // Create transaction
        $itemType = $validated['saleable_type'] === 'App\Models\Bird' ? 'birds' : 'egg crates';
        Transaction::create([
            'type' => 'sale',
            'amount' => $validated['total_amount'],
            'status' => 'pending',
            'date' => $validated['sale_date'],
            'source_type' => Sale::class,
            'source_id' => $sale->id,
            'user_id' => Auth::id(),
            'description' => "Sale of {$validated['quantity']} {$itemType} to {$customer->name}",
        ]);

        // Create income record
        Income::create([
            'source' => "Sale #{$sale->id}",
            'description' => "Sale of {$validated['quantity']} {$itemType} to {$customer->name}",
            'amount' => $validated['total_amount'],
            'date' => $validated['sale_date'],
            'created_by' => Auth::id(),
        ]);

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_sale',
            'details' => "Created sale #{$sale->id} for {$customer->name} (Total: {$validated['total_amount']})",
        ]);

        // Create alert
        Alert::create([
            'message' => "New sale #{$sale->id} for customer {$customer->name}",
            'type' => 'sale',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function edit(Sale $sale)
    {
        $birds = Bird::where('quantity', '>', 0)->orWhere('id', $sale->saleable_id)->get();
        $eggs = Egg::where('crates', '>', 0)->orWhere('id', $sale->saleable_id)->get();
        $customers = Customer::orderBy('name')->get();
        return view('sales.edit', compact('sale', 'birds', 'eggs', 'customers'));
    }

    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        if (!Auth::check()) {
            return back()->withErrors(['auth' => 'User must be authenticated to update a sale.']);
        }

        $validated = $request->validated();

        // Validate stock availability
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
            ['phone' => $validated['customer_phone'] ?? '', 'email' => $validated['customer_email'] ?? '']
        );

        // Update stock
        if ($quantityDiff != 0 || $sale->saleable_id != $validated['saleable_id'] || $sale->saleable_type != $validated['saleable_type']) {
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

        // Update sale
        $validated['customer_id'] = $customer->id;
        $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];
        $validated['due_date'] = $validated['due_date'] ?? ($sale->due_date ?? Carbon::parse($validated['sale_date'])->addDays(7));

        $sale->update($validated);
        $sale->updatePaymentStatus();

        // Update or create transaction
        $itemType = $validated['saleable_type'] === 'App\Models\Bird' ? 'birds' : 'egg crates';
        Transaction::updateOrCreate(
            [
                'source_type' => Sale::class,
                'source_id' => $sale->id,
            ],
            [
                'type' => 'sale',
                'amount' => $validated['total_amount'],
                'status' => $sale->status,
                'date' => $validated['sale_date'],
                'user_id' => Auth::id(),
                'description' => "Updated sale of {$validated['quantity']} {$itemType} to {$customer->name}",
            ]
        );

        // Update or create income record
        Income::updateOrCreate(
            [
                'source' => "Sale #{$sale->id}",
            ],
            [
                'description' => "Sale of {$validated['quantity']} {$itemType} to {$customer->name}",
                'amount' => $validated['total_amount'],
                'date' => $validated['sale_date'],
                'created_by' => Auth::id(),
            ]
        );

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_sale',
            'details' => "Updated sale #{$sale->id} for {$customer->name} (Total: {$validated['total_amount']})",
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale)
    {
        if (!Auth::check()) {
            return back()->withErrors(['auth' => 'User must be authenticated to delete a sale.']);
        }

        if ($sale->payments()->exists()) {
            return redirect()->route('sales.index')->with('error', 'Cannot delete sale with associated payments.');
        }

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

        // Delete transaction
        Transaction::where('source_type', Sale::class)
            ->where('source_id', $sale->id)
            ->delete();

        // Delete income record
        Income::where('source', "Sale #{$sale->id}")->delete();

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_sale',
            'details' => "Deleted sale #{$sale->id} for {$sale->customer->name} (Total: {$sale->total_amount})",
        ]);

        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }

    public function invoice(Sale $sale, Request $request)
    {
        $sale->load('customer', 'saleable', 'payments');

        if (!$sale->customer || !$sale->saleable) {
            return redirect()->back()->with('error', 'Customer or product not found for this sale.');
        }

        $company = [
            'name' => config('app.name', 'Poultry Tracker'),
            'address' => 'Aprah Opeicuma, Awutu Senya West',
            'phone' => '0593036689',
            'email' => 'info@poultrytracker.local',
        ];

        $customerName = Str::slug($sale->customer->name, '_');
        $saleDate = $sale->sale_date->format('Y-m-d');
        $filename = "invoice-{$customerName}-{$saleDate}.pdf";

        if ($request->query('preview', false)) {
            return view('sales.invoice', compact('sale', 'company'));
        }

        $pdf = Pdf::loadView('sales.invoice', compact('sale', 'company'));

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'generated_invoice',
            'details' => "Generated invoice for sale #{$sale->id} for {$sale->customer->name}",
        ]);

        return $pdf->download($filename);
    }

    public function updateStatus(Request $request, Sale $sale)
    {
        if (!Auth::check()) {
            return back()->withErrors(['auth' => 'User must be authenticated to update invoice status.']);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,paid,partially_paid,overdue',
        ]);

        $oldStatus = $sale->status;
        $sale->update(['status' => $validated['status']]);

        // Update transaction status
        Transaction::where('source_type', Sale::class)
            ->where('source_id', $sale->id)
            ->update(['status' => $validated['status']]);

        // Update income status if necessary
        Income::where('source', "Sale #{$sale->id}")
            ->update(['synced_at' => $validated['status'] === 'paid' ? now() : null]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_invoice_status',
            'details' => "Changed status of invoice #{$sale->id} for {$sale->customer->name} from {$oldStatus} to {$validated['status']}",
        ]);

        Alert::create([
            'message' => "Invoice #{$sale->id} status changed to {$validated['status']} for {$sale->customer->name}",
            'type' => 'payment',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice status updated successfully.');
    }

    public function recordPayment(Request $request, Sale $sale)
    {
        if (!Auth::check()) {
            return back()->withErrors(['auth' => 'User must be authenticated to record a payment.']);
        }

        if ($sale->isPaid()) {
            return redirect()->route('invoices.index')->with('error', 'Invoice is already fully paid.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($sale->total_amount - $sale->paid_amount),
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|in:cash,bank_transfer,mobile_money|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Record the payment
        $payment = Payment::create([
            'sale_id' => $sale->id,
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update the sale's paid amount
        $sale->increment('paid_amount', $validated['amount']);
        $sale->refresh();

        // Automatically update status
        $sale->updatePaymentStatus();

        // Update income synced_at if sale is fully paid
        if ($sale->isPaid()) {
            Income::where('source', "Sale #{$sale->id}")
                ->update(['synced_at' => now()]);
        }

        // Log the activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'recorded_payment',
            'details' => "Recorded payment of GH₵ {$validated['amount']} for sale #{$sale->id} to {$sale->customer->name}",
        ]);

        // Create alert
        Alert::create([
            'message' => "Payment of GH₵ {$validated['amount']} recorded for invoice #{$sale->id} ({$sale->customer->name})",
            'type' => 'payment',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('invoices.index')->with('success', 'Payment recorded successfully.');
    }
}