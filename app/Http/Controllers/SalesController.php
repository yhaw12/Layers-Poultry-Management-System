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

            $sales = $query->orderBy('sale_date', 'desc')->paginate(10)->withQueryString();

            $totalAmountKey = "sales_total_amount_{$start}_{$end}_{$request->status}";
            $totalAmount = null;
            try {
                if (method_exists(Cache::store(), 'tags')) {
                    $totalAmount = Cache::tags(['sales'])->remember($totalAmountKey, 300, function () use ($start, $end, $request) {
                        $q = Sale::whereBetween('sale_date', [$start, $end])->whereNull('deleted_at');
                        if ($request->status) $q->where('status', $request->status);
                        return (float) $q->sum('total_amount');
                    });
                } else {
                    $totalAmount = Cache::remember($totalAmountKey, 300, function () use ($start, $end, $request) {
                        $q = Sale::whereBetween('sale_date', [$start, $end])->whereNull('deleted_at');
                        if ($request->status) $q->where('status', $request->status);
                        return (float) $q->sum('total_amount');
                    });
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to use cache tags for sales totals: ' . $e->getMessage());
                $totalAmount = (float) Sale::whereBetween('sale_date', [$start, $end])->whereNull('deleted_at')->sum('total_amount');
            }

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
        // Load available birds/eggs (scopeAvailable on models, see model changes below)
        $birds = Bird::available()->orderBy('breed')->get(); // collection of Bird models
        $eggs  = Egg::available()->orderBy('date_laid', 'desc')->get();

        // human-friendly option lists (if you prefer server-side build for selects)
        $birdOptions = $birds->mapWithKeys(function ($b) {
            return [$b->id => $b->displayName()];
        });

        $eggOptions = $eggs->mapWithKeys(function ($e) {
            return [$e->id => $e->displayName()];
        });

        // JS-safe data arrays (simple arrays — safe to pass to @json)
        $birdsData = $birds->map(function ($b) {
            return [
                'id' => $b->id,
                'breed' => $b->breed,
                'type' => $b->type,
                'quantity' => (int) $b->quantity,
                'stage' => $b->stage ?? null,
                'display' => $b->displayName(),
            ];
        })->values()->toArray();

        $eggsData = $eggs->map(function ($e) {
            return [
                'id' => $e->id,
                'crates' => (int) $e->crates,
                'additional_eggs' => (int) ($e->additional_eggs ?? 0),
                'date_laid' => optional($e->date_laid)->format('Y-m-d'),
                'pen_name' => optional($e->pen)->name,
                'is_cracked' => (bool) ($e->is_cracked ?? false),
                'egg_size' => $e->egg_size ?? null,
                'display' => $e->displayName(),
            ];
        })->values()->toArray();

        $customers = Customer::whereNull('deleted_at')->orderBy('name')->get();

        return view('sales.create', compact('birds', 'eggs', 'birdOptions', 'eggOptions', 'customers', 'birdsData', 'eggsData'));
    }

    public function store(StoreSaleRequest $request)
    {
        DB::beginTransaction();
        try {
            if (!Auth::check()) {
                return back()->withErrors(['auth' => 'User must be authenticated to record a sale.']);
            }

            $validated = $request->validated();

            // normalize saleable_type to class name to be safe
            $saleableType = ltrim($validated['saleable_type'], '\\');

            // Validate stock BEFORE creating anything
            if ($saleableType === Bird::class || $saleableType === 'App\\Models\\Bird') {
                $bird = Bird::lockForUpdate()->find($validated['saleable_id']);
                if (!$bird || $bird->quantity < $validated['quantity']) {
                    DB::rollBack();
                    return back()->withErrors(['quantity' => 'Insufficient bird stock available.']);
                }
                // Validate product_variant matches bird's stage or type
                $product_variant = $validated['product_variant'] ?? '';
                if ($product_variant && $product_variant !== $bird->stage && $product_variant !== $bird->type) {
                    throw ValidationException::withMessages(['product_variant' => 'Selected variant does not match the bird\'s stage or type.']);
                }
            } else {
                $egg = Egg::lockForUpdate()->find($validated['saleable_id']);
                if (!$egg || $egg->crates < $validated['quantity']) {
                    DB::rollBack();
                    return back()->withErrors(['quantity' => 'Insufficient egg crates available.']);
                }
                // Validate product_variant matches egg's is_cracked
                $product_variant = $validated['product_variant'] ?? '';
                $expected_variant = $egg->is_cracked ? 'cracked' : 'regular';
                if ($product_variant && $product_variant !== $expected_variant) {
                    throw ValidationException::withMessages(['product_variant' => 'Selected variant does not match if the eggs are cracked or not.']);
                }
            }

            // create or resolve customer
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
            $validated['cashier_id'] = Auth::id(); // <-- set cashier on sale

            $sale = Sale::create($validated);


            // decrement stock
            if (isset($bird) && ($saleableType === Bird::class || $saleableType === 'App\\Models\\Bird')) {
                $bird->decrement('quantity', $validated['quantity']);
                if ($bird->fresh()->quantity < 0) {
                    $bird->update(['quantity' => 0]);
                }
            } elseif (isset($egg)) {
                $egg->decrement('crates', $validated['quantity']);
                if ($egg->fresh()->crates <= 0) {
                    $egg->update(['crates' => 0]);
                    // optional: $egg->delete();
                }
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
                'details' => "Created sale #{$sale->id} for {$customer->name} (Total: ₵ {$validated['total_amount']})",
            ]);

            Alert::create([
                'message' => "New sale #{$sale->id} for customer {$customer->name}",
                'type' => 'sale',
                'user_id' => Auth::id(),
            ]);

            // Record initial payment if provided
            if ($request->filled('payment_amount') && $request->payment_amount > 0) {
                $paymentValidated = $request->validate([
                    'payment_amount' => 'required|numeric|min:0.01|max:' . $validated['total_amount'],
                    'payment_date' => 'required|date',
                    'payment_method' => 'nullable|string|in:cash,bank_transfer,mobile_money|max:255',
                ]);

                $paymentRequest = new Request($paymentValidated);
                $paymentRequest->merge(['amount' => $paymentValidated['payment_amount']]);
                $this->recordPayment($paymentRequest, $sale);
            }

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
        $birds = Bird::available()->orderBy('breed')->get()
        ->pushIf(! Bird::available()->pluck('id')->contains($sale->saleable_id) && ($sale->saleable_type === Bird::class || $sale->saleable_type === 'App\\Models\\Bird'),
                 Bird::find($sale->saleable_id))
        ->filter();

    $eggs = Egg::available()->orderBy('date_laid', 'desc')->get()
        ->pushIf(! Egg::available()->pluck('id')->contains($sale->saleable_id) && ($sale->saleable_type === Egg::class || $sale->saleable_type === 'App\\Models\\Egg'),
                 Egg::find($sale->saleable_id))
        ->filter();

    // normalize collections (filter nulls)
    $birds = $birds->filter();
    $eggs = $eggs->filter();

    // JS-friendly arrays
    $birdsData = $birds->map(function ($b) {
        return [
            'id' => $b->id,
            'breed' => $b->breed,
            'type' => $b->type,
            'quantity' => (int) $b->quantity,
            'stage' => $b->stage ?? null,
            'display' => $b->displayName(),
        ];
    })->values()->toArray();

    $eggsData = $eggs->map(function ($e) {
        return [
            'id' => $e->id,
            'crates' => (int) $e->crates,
            'additional_eggs' => (int) ($e->additional_eggs ?? 0),
            'date_laid' => optional($e->date_laid)->format('Y-m-d'),
            'pen_name' => optional($e->pen)->name,
            'is_cracked' => (bool) ($e->is_cracked ?? false),
            'egg_size' => $e->egg_size ?? null,
            'display' => $e->displayName(),
        ];
    })->values()->toArray();

    $customers = Customer::whereNull('deleted_at')->orderBy('name')->get();

    return view('sales.edit', compact('sale', 'birds', 'eggs', 'customers', 'birdsData', 'eggsData'));

    }

    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        DB::beginTransaction();
        try {
            if (!Auth::check()) {
                return back()->withErrors(['auth' => 'User must be authenticated to update a sale.']);
            }

            $validated = $request->validated();

            $newType = ltrim($validated['saleable_type'], '\\');
            $quantityDiff = $validated['quantity'] - $sale->quantity;

            if ($quantityDiff > 0) {
                if ($newType === Bird::class || $newType === 'App\\Models\\Bird') {
                    $newBird = Bird::lockForUpdate()->find($validated['saleable_id']);
                    if (!$newBird || $newBird->quantity < $quantityDiff) {
                        DB::rollBack();
                        return back()->withErrors(['quantity' => 'Insufficient bird stock available.']);
                    }
                    // Validate product_variant matches newBird's stage or type
                    $product_variant = $validated['product_variant'] ?? '';
                    if ($product_variant && $product_variant !== $newBird->stage && $product_variant !== $newBird->type) {
                        throw ValidationException::withMessages(['product_variant' => 'Selected variant does not match the bird\'s stage or type.']);
                    }
                } else {
                    $newEgg = Egg::lockForUpdate()->find($validated['saleable_id']);
                    if (!$newEgg || $newEgg->crates < $quantityDiff) {
                        DB::rollBack();
                        return back()->withErrors(['quantity' => 'Insufficient egg crates available.']);
                    }
                    // Validate product_variant matches newEgg's is_cracked
                    $product_variant = $validated['product_variant'] ?? '';
                    $expected_variant = $newEgg->is_cracked ? 'cracked' : 'regular';
                    if ($product_variant && $product_variant !== $expected_variant) {
                        throw ValidationException::withMessages(['product_variant' => 'Selected variant does not match if the eggs are cracked or not.']);
                    }
                }
            }

            if ($sale->saleable_type === Bird::class || $sale->saleable_type === 'App\\Models\\Bird') {
                $originalBird = Bird::lockForUpdate()->find($sale->saleable_id);
                if ($originalBird) {
                    $originalBird->increment('quantity', $sale->quantity);
                }
            } else {
                $originalEgg = Egg::lockForUpdate()->find($sale->saleable_id);
                if ($originalEgg) {
                    $originalEgg->increment('crates', $sale->quantity);
                }
            }

            if ($newType === Bird::class || $newType === 'App\\Models\\Bird') {
                $bird = Bird::lockForUpdate()->find($validated['saleable_id']);
                if ($bird) {
                    if ($bird->quantity < $validated['quantity']) {
                        DB::rollBack();
                        return back()->withErrors(['quantity' => 'Insufficient bird stock available for requested update.']);
                    }
                    $bird->decrement('quantity', $validated['quantity']);
                    if ($bird->fresh()->quantity < 0) {
                        $bird->update(['quantity' => 0]);
                    }
                }
            } else {
                $egg = Egg::lockForUpdate()->find($validated['saleable_id']);
                if ($egg) {
                    if ($egg->crates < $validated['quantity']) {
                        DB::rollBack();
                        return back()->withErrors(['quantity' => 'Insufficient egg crates available for requested update.']);
                    }
                    $egg->decrement('crates', $validated['quantity']);
                    if ($egg->fresh()->crates <= 0) {
                        $egg->update(['crates' => 0]);
                    }
                }
            }

            $customer = Customer::firstOrCreate(
                ['name' => $validated['customer_name']],
                ['phone' => $validated['customer_phone'] ?? '', 'email' => $validated['customer_email'] ?? '']
            );

            $validated['customer_id'] = $customer->id;
            $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];
            $validated['due_date'] = $validated['due_date'] ?? ($sale->due_date ?? Carbon::parse($validated['sale_date'])->addDays(7));
            $validated['created_by'] = Auth::id();

            $sale->update($validated);
            $sale->updatePaymentStatus();

            $itemType = $validated['saleable_type'] === Bird::class || $validated['saleable_type'] === 'App\\Models\\Bird' ? 'birds' : 'egg crates';
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
                'details' => "Updated sale #{$sale->id} for {$customer->name} (Total: ₵ {$validated['total_amount']})",
            ]);

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update sale', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to update sale.');
        }
    }

    public function destroy(Request $request, Sale $sale)
{
    try {
        if (!Auth::check()) {
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Authentication required'], 401)
                : back()->withErrors(['auth' => 'User must be authenticated to delete a sale.']);
        }

        // Prevent deleting sales that already have payments
        if ($sale->payments()->exists()) {
            $message = 'Cannot delete sale with associated payments.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : redirect()->route('sales.index')->with('error', $message);
        }

        DB::beginTransaction();

        // Restore stock with row-level lock to avoid races
        if ($sale->saleable_type === Bird::class || $sale->saleable_type === 'App\\Models\\Bird') {
            $bird = Bird::lockForUpdate()->find($sale->saleable_id);
            if ($bird) {
                $bird->increment('quantity', $sale->quantity);
            }
        } else {
            $egg = Egg::lockForUpdate()->find($sale->saleable_id);
            if ($egg) {
                $egg->increment('crates', $sale->quantity);
            }
        }

        // Remove related transaction/income records
        Transaction::where('source_type', Sale::class)
            ->where('source_id', $sale->id)
            ->delete();

        Income::where('source', "Sale #{$sale->id}")->delete();

        // Activity log
        $customerName = $sale->customer ? $sale->customer->name : 'Unknown';
        $saleDate = $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') : 'N/A';

        UserActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'deleted_sale',
            'details' => "Deleted sale ID {$sale->id} for customer {$customerName} on {$saleDate} with total amount ₵{$sale->total_amount}",
        ]);

        // Soft-delete the sale
        $sale->delete();

        DB::commit();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Sale record deleted successfully.',
                'sale_id' => $sale->id,
            ], 200);
        }

        return redirect()->route('sales.index')->with('success', 'Sale record deleted successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to delete sale: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'sale_id' => $sale->id ?? null]);

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sale. ' . ($e->getCode() == 23000 ? 'This sale is linked to other data.' : 'Please try again.')
            ], 500);
        }

        return redirect()->route('sales.index')->with('error', 'Failed to delete sale.');
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


            $pdf = Pdf::loadView('sales.invoice', compact('sale', 'company'));
            
            if ($request->query('preview')) {
                return view('sales.invoice_fragment', compact('sale', 'company'));
            }

            return view('sales.invoice', compact('sale', 'company'));

            

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
    public function recordPayment(Request $request, Sale $sale)
    {
        try {
            if (!Auth::check()) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Authentication required'], 401);
                }
                return back()->withErrors(['auth' => 'User must be authenticated to record a payment.']);
            }

            // Refresh sale so we work with the latest values
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
                'notes' => 'nullable|string|max:1000',
            ]);

            $amount = round((float) $validated['amount'], 2);

            // compute current balance from DB to avoid race conditions
            $balance = round((float) $sale->total_amount - (float) $sale->paid_amount, 2);

            if ($amount > $balance) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Payment amount exceeds outstanding balance.', 'balance' => $balance], 422);
                }
                return back()->withErrors(['amount' => 'Payment amount exceeds outstanding balance.']);
            }

            DB::beginTransaction();

            // Create payment (persist)
            $payment = Payment::create([
                'sale_id'        => $sale->id,
                'customer_id'    => $sale->customer_id ?? ($sale->customer ? $sale->customer->id : null),
                'amount'         => $amount,
                'payment_date'   => $validated['payment_date'],
                'payment_method' => $validated['payment_method'] ?? null,
                'notes'          => $validated['notes'] ?? null,
                'created_by'     => Auth::id(),
            ]);

            // Create a transaction record to represent this payment.
            // This is the canonical "payment" record used for payment history lookups.
            $paymentTransaction = Transaction::create([
                'type' => 'payment',
                'amount' => $amount,
                'status' => 'approved', // payments are recorded as approved
                'date' => $validated['payment_date'],
                'source_type' => Sale::class,
                'source_id' => $sale->id,
                'user_id' => Auth::id(),
                'description' => "Payment of ₵ {$amount} for Sale #{$sale->id}",
            ]);

            // Recalculate paid_amount from payments (ensures correctness even with concurrent edits)
            // Use payment_date as asOf so future-dated payments behave correctly
            $asOf = Carbon::parse($payment->payment_date ?? now());
            $sale->recalculatePaidAmount($asOf);
            $sale->updatePaymentStatus($asOf);

            // Ensure cashier is recorded on the Sale if missing
            if (empty($sale->cashier_id)) {
                $sale->update(['cashier_id' => Auth::id()]);
            }

            // If sale just became fully paid, sync income record if needed
            if ($sale->isPaid()) {
                Income::where('source', "Sale #{$sale->id}")
                    ->update(['synced_at' => now()]);
            }

            $customerName = $sale->customer ? $sale->customer->name : 'Unknown';

            // Activity log
            $detailText = "Recorded payment of ₵ {$amount} (Payment ID: {$payment->id}) for Sale #{$sale->id} to {$customerName}. New paid_amount: ₵ {$sale->paid_amount} (Balance: ₵ {$sale->balance()}).";
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'recorded_payment',
                'details' => $detailText,
            ]);

            Alert::create([
                'message' => "Payment of ₵ {$amount} recorded for invoice #{$sale->id} ({$customerName})",
                'type' => 'payment',
                'user_id' => Auth::id(),
            ]);

            // Targeted cache invalidation:
            try {
                if (method_exists(Cache::store(), 'tags')) {
                    Cache::tags(['sales'])->flush();
                } else {
                    Log::warning('Cache store does not support tags; no targeted sales cache flush was performed.');
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to flush sales cache tags: ' . $e->getMessage());
            }

            DB::commit();

            // fresh sale so front-end gets the latest cashier relation
            $sale->refresh();
            $cashierName = optional($sale->cashier)->name ?? null;

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment recorded successfully.',
                    'sale_id' => $sale->id,
                    'payment_id' => $payment->id,
                    'payment_transaction_id' => $paymentTransaction->id,
                    'paid_amount' => (float) $sale->paid_amount,
                    'total_amount' => (float) $sale->total_amount,
                    'balance' => (float) $sale->balance(),
                    'status' => $sale->status,
                    'cashier_id' => $sale->cashier_id,
                    'cashier_name' => $cashierName,
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
                'sale_id' => $sale->id ?? null,
                'request' => $request->all(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to record payment: ' . $e->getMessage()], 500);
            }

            return back()->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

}