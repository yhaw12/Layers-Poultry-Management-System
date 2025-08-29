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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'nullable|date|before_or_equal:end_date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'status' => 'nullable|in:pending,paid,partially_paid,overdue',
            ]);

            $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());

            $query = Sale::with('customer', 'saleable', 'payments')
                ->whereBetween('sale_date', [$start, $end])
                ->whereNull('deleted_at');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            // Do not cache paginators directly — paginate live
            $sales = $query->orderBy('sale_date', 'desc')->paginate(10)->withQueryString();

            // Totals: cache these small aggregates (optional)
            $totalAmountKey = "sales_total_amount_{$start}_{$end}_{$request->status}";
            $totalAmount = Cache::remember($totalAmountKey, 300, function () use ($start, $end, $request) {
                $q = Sale::whereBetween('sale_date', [$start, $end])->whereNull('deleted_at');
                if ($request->status) $q->where('status', $request->status);
                return (float) $q->sum('total_amount');
            });

            $company = [
                'name' => config('app.name', 'Poultry Tracker'),
                'address' => 'Aprah Opeicuma, Awutu Senya West',
                'phone' => '0593036689',
                'email' => 'info@poultrytracker.local',
            ];

            return view('sales.index', compact('sales', 'start', 'end', 'company', 'totalAmount'));
        } catch (\Exception $e) {
            Log::error('Failed to load sales', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load sales.');
        }
    }

    public function sales(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'nullable|date|before_or_equal:end_date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());
            $cacheKey = "egg_sales_{$start}_{$end}";

            $sales = Sale::with('customer', 'saleable')
                ->where('saleable_type', Egg::class)
                ->whereBetween('sale_date', [$start, $end])
                ->whereNull('deleted_at')
                ->orderBy('sale_date', 'desc')
                ->paginate(10);

            $totalSales = (float) Sale::where('saleable_type', Egg::class)
                ->whereBetween('sale_date', [$start, $end])
                ->whereNull('deleted_at')
                ->sum('total_amount');

            $totalCratesSold = (float) Sale::where('saleable_type', Egg::class)
                ->whereBetween('sale_date', [$start, $end])
                ->whereNull('deleted_at')
                ->sum('quantity');

            return view('eggs.sales', compact('sales', 'totalSales', 'totalCratesSold', 'start', 'end'));
        } catch (\Exception $e) {
            Log::error('Failed to load egg sales', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load egg sales.');
        }
    }

    public function birdSales(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'nullable|date|before_or_equal:end_date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());
            $cacheKey = "bird_sales_{$start}_{$end}";

            $sales = Sale::with('customer', 'saleable')
                ->where('saleable_type', Bird::class)
                ->whereBetween('sale_date', [$start, $end])
                ->whereNull('deleted_at')
                ->orderBy('sale_date', 'desc')
                ->paginate(10);

            $totalSales = (float) Sale::where('saleable_type', Bird::class)
                ->whereBetween('sale_date', [$start, $end])
                ->whereNull('deleted_at')
                ->sum('total_amount');

            $totalQuantity = (float) Sale::where('saleable_type', Bird::class)
                ->whereBetween('sale_date', [$start, $end])
                ->whereNull('deleted_at')
                ->sum('quantity');

            return view('sales.birds', compact('sales', 'totalSales', 'totalQuantity', 'start', 'end'));
        } catch (\Exception $e) {
            Log::error('Failed to load bird sales', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load bird sales.');
        }
    }

    public function create()
    {
        $birds = Bird::where('quantity', '>', 0)->whereNull('deleted_at')->get();
        $eggs = Egg::where('crates', '>', 0)->whereNull('deleted_at')->get();
        $customers = Customer::whereNull('deleted_at')->orderBy('name')->get();
        return view('sales.create', compact('birds', 'eggs', 'customers'));
    }

    public function store(StoreSaleRequest $request)
    {
        DB::beginTransaction();
        try {
            if (!Auth::check()) {
                return back()->withErrors(['auth' => 'User must be authenticated to record a sale.']);
            }

            $validated = $request->validated();

            if ($validated['saleable_type'] === Bird::class || $validated['saleable_type'] === 'App\Models\Bird') {
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

            $customer = Customer::firstOrCreate(
                ['name' => $validated['customer_name']],
                ['phone' => $validated['customer_phone'] ?? '', 'email' => $validated['customer_email'] ?? '']
            );

            $validated['customer_id'] = $customer->id;
            $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];
            $validated['due_date'] = $validated['due_date'] ?? Carbon::parse($validated['sale_date'])->addDays(7);
            $validated['paid_amount'] = 0;
            $validated['status'] = 'pending';
            $validated['created_by'] = Auth::id();

            $sale = Sale::create($validated);

            if (isset($bird) && $validated['saleable_type'] === Bird::class) {
                $bird->decrement('quantity', $validated['quantity']);
            } elseif (isset($egg)) {
                $egg->decrement('crates', $validated['quantity']);
            }

            Transaction::create([
                'type' => 'sale',
                'amount' => $validated['total_amount'],
                'status' => 'pending',
                'date' => $validated['sale_date'],
                'source_type' => Sale::class,
                'source_id' => $sale->id,
                'user_id' => Auth::id(),
                'description' => "Sale of {$validated['quantity']} to {$customer->name}",
            ]);

            Income::create([
                'source' => "Sale #{$sale->id}",
                'description' => "Sale of {$validated['quantity']} to {$customer->name}",
                'amount' => $validated['total_amount'],
                'date' => $validated['sale_date'],
                'created_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_sale',
                'details' => "Created sale #{$sale->id} for {$customer->name} (Total: GHS {$validated['total_amount']})",
            ]);

            Alert::create([
                'message' => "New sale #{$sale->id} for customer {$customer->name}",
                'type' => 'sale',
                'user_id' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store sale', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to record sale.');
        }
    }

    public function edit(Sale $sale)
    {
        $birds = Bird::where('quantity', '>', 0)->whereNull('deleted_at')->orWhere('id', $sale->saleable_id)->get();
        $eggs = Egg::where('crates', '>', 0)->whereNull('deleted_at')->orWhere('id', $sale->saleable_id)->get();
        $customers = Customer::whereNull('deleted_at')->orderBy('name')->get();
        return view('sales.edit', compact('sale', 'birds', 'eggs', 'customers'));
    }

    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        try {
            if (!Auth::check()) {
                return back()->withErrors(['auth' => 'User must be authenticated to update a sale.']);
            }

            $validated = $request->validated();

            $quantityDiff = $validated['quantity'] - $sale->quantity;
            if ($quantityDiff > 0) {
                if ($validated['saleable_type'] === Bird::class || $validated['saleable_type'] === 'App\Models\Bird') {
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

            $customer = Customer::firstOrCreate(
                ['name' => $validated['customer_name']],
                ['phone' => $validated['customer_phone'] ?? '', 'email' => $validated['customer_email'] ?? '']
            );

            if ($quantityDiff != 0 || $sale->saleable_id != $validated['saleable_id'] || $sale->saleable_type != $validated['saleable_type']) {
                if ($sale->saleable_type === Bird::class || $sale->saleable_type === 'App\Models\Bird') {
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

                if ($validated['saleable_type'] === Bird::class || $validated['saleable_type'] === 'App\Models\Bird') {
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

            $validated['customer_id'] = $customer->id;
            $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];
            $validated['due_date'] = $validated['due_date'] ?? ($sale->due_date ?? Carbon::parse($validated['sale_date'])->addDays(7));
            $validated['created_by'] = Auth::id();

            $sale->update($validated);
            $sale->updatePaymentStatus();

            $itemType = $validated['saleable_type'] === Bird::class ? 'birds' : 'egg crates';
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

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated_sale',
                'details' => "Updated sale #{$sale->id} for {$customer->name} (Total: GHS {$validated['total_amount']})",
            ]);

            return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update sale', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to update sale.');
        }
    }

    public function destroy(Sale $sale)
    {
        try {
            if (!Auth::check()) {
                return back()->withErrors(['auth' => 'User must be authenticated to delete a sale.']);
            }

            if ($sale->payments()->exists()) {
                return redirect()->route('sales.index')->with('error', 'Cannot delete sale with associated payments.');
            }

            if ($sale->saleable_type === Bird::class || $sale->saleable_type === 'App\Models\Bird') {
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

            Transaction::where('source_type', Sale::class)
                ->where('source_id', $sale->id)
                ->delete();

            Income::where('source', "Sale #{$sale->id}")->delete();

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted_sale',
                'details' => "Deleted sale #{$sale->id} for {$sale->customer->name} (Total: GHS {$sale->total_amount})",
            ]);

            $sale->delete();
            return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete sale', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to delete sale.');
        }
    }

    public function invoice(Sale $sale, Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Failed to generate invoice', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to generate invoice.');
        }
    }

    public function updateStatus(Request $request, Sale $sale)
    {
        try {
            if (!Auth::check()) {
                return back()->withErrors(['auth' => 'User must be authenticated to update invoice status.']);
            }

            $validated = $request->validate([
                'status' => 'required|in:pending,paid,partially_paid,overdue',
            ]);

            $oldStatus = $sale->status;
            $sale->update(['status' => $validated['status']]);

            Transaction::where('source_type', Sale::class)
                ->where('source_id', $sale->id)
                ->update(['status' => $validated['status']]);

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

            return redirect()->route('sales.index')->with('success', 'Invoice status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update invoice status', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to update invoice status.');
        }
    }


    /**
 * Record payment (supports AJAX JSON responses)
 */
/**
 * Record payment (supports AJAX JSON responses)
 */
public function recordPayment(Request $request, Sale $sale)
{
    try {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Authentication required'], 401);
            }
            return back()->withErrors(['auth' => 'User must be authenticated to record a payment.']);
        }

        // Refresh sale to ensure latest paid_amount
        $sale->refresh();

        if ($sale->isPaid()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Invoice is already fully paid.'], 422);
            }
            return redirect()->route('sales.index')->with('error', 'Invoice is already fully paid.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|in:cash,bank_transfer,mobile_money|max:255',
        ]);

        $amount = round((float) $validated['amount'], 2);
        $balance = round((float) $sale->total_amount - (float) $sale->paid_amount, 2);

        if ($amount > $balance) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Payment amount exceeds outstanding balance.', 'balance' => $balance], 422);
            }
            return back()->withErrors(['amount' => 'Payment amount exceeds outstanding balance.']);
        }

        DB::beginTransaction();

        // Create payment and include customer_id & created_by
        $payment = Payment::create([
            'sale_id'        => $sale->id,
            'customer_id'    => $sale->customer_id ?? ($sale->customer ? $sale->customer->id : null),
            'amount'         => $amount,
            'payment_date'   => $validated['payment_date'],
            'payment_method' => $validated['payment_method'] ?? null,
            'notes'          => $request->input('notes', null),
            // 'created_by'     => Auth::id(),
        ]);

        // Increment paid amount and update sale status
        $sale->increment('paid_amount', $amount);
        $sale->refresh();
        $sale->updatePaymentStatus();

        if ($sale->isPaid()) {
            Income::where('source', "Sale #{$sale->id}")
                ->update(['synced_at' => now()]);
        }

        $customerName = $sale->customer ? $sale->customer->name : 'Unknown';

        // Create activity log with details (ensure 'details' is provided)
        $detailText = "Recorded payment of GHS {$amount} (Payment ID: {$payment->id}) for Sale #{$sale->id} to {$customerName}. New paid_amount: GHS {$sale->paid_amount} (Balance: GHS {$sale->balance()}).";
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'recorded_payment',
            'details' => $detailText,
        ]);

        Alert::create([
            'message' => "Payment of GHS {$amount} recorded for invoice #{$sale->id} ({$customerName})",
            'type' => 'payment',
            'user_id' => Auth::id(),
        ]);

        DB::commit();

        // Invalidate or selectively clear caches
        Cache::flush();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully.',
                'sale_id' => $sale->id,
                'payment_id' => $payment->id,
                'paid_amount' => (float) $sale->paid_amount,
                'total_amount' => (float) $sale->total_amount,
                'balance' => (float) $sale->balance(),
                'status' => $sale->status,
            ]);
        }

        return redirect()->route('sales.index')->with('success', 'Payment recorded successfully.');
    } catch (\Illuminate\Validation\ValidationException $ve) {
        if ($request->expectsJson()) {
            return response()->json(['errors' => $ve->errors()], 422);
        }
        throw $ve;
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to record payment', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'sale_id' => $sale->id,
            'request' => $request->all(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Failed to record payment: ' . $e->getMessage()], 500);
        }

        return back()->with('error', 'Failed to record payment: ' . $e->getMessage());
    }
}


}
