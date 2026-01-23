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
use App\Models\SaleItem;
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
                'end_date'   => 'nullable|date|after_or_equal:start_date',
                'status'     => 'nullable|in:pending,paid,partially_paid,overdue',
                'search'     => 'nullable|string|max:255', 
            ]);

            $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
            $end   = $request->input('end_date', now()->endOfMonth()->toDateString());
            $search = $request->input('search');

            $query = Sale::with(['customer', 'saleable', 'payments'])
                ->whereBetween('sale_date', [$start, $end])
                ->whereNull('deleted_at');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($subQ) use ($search) {
                          $subQ->where('name', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");
                      });
                });
            }

            $sales = $query->orderBy('sale_date', 'desc')->paginate(10)->withQueryString();
            
            // Calculate total from the filtered query
            $totalAmount = (float) $query->sum('total_amount');

            return view('sales.index', compact('sales', 'start', 'end', 'totalAmount'));
        } catch (\Exception $e) {
            Log::error('Failed to load sales', ['error' => $e->getMessage()]);
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

    // --- In SalesController.php ---

    public function store(StoreSaleRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            
            $customer = \App\Models\Customer::firstOrCreate(
                ['name' => $validated['customer_name']],
                ['phone' => $validated['customer_phone'] ?? '', 'email' => $validated['customer_email'] ?? '']
            );

            $sale = \App\Models\Sale::create([
                'customer_id'     => $customer->id,
                'saleable_type'   => $validated['saleable_type'],
                'saleable_id'     => $validated['saleable_id'], // Primary reference
                'quantity'        => $validated['quantity'],
                'unit_price'      => $validated['unit_price'],
                'total_amount'    => $validated['quantity'] * $validated['unit_price'],
                'sale_date'       => $validated['sale_date'],
                'due_date'        => $validated['due_date'] ?? now()->addDays(7),
                'product_variant' => $validated['product_variant'],
                'status'          => 'pending',
                'created_by'      => Auth::id(),
            ]);

            // Inventory Deduction
            $this->handleInventoryDeduction($sale, $validated);

            // One-step Payment
            if ($request->filled('payment_amount') && $request->payment_amount > 0) {
                $this->executePaymentLogic($sale, $request->payment_amount, $request->payment_date ?? now(), $request->payment_method ?? 'cash');
            }

            $this->logSaleFinancials($sale, $customer);

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Sale Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Critical Error: ' . $e->getMessage());
        }
    }

    /**
     * Updated AJAX method for the Index page
     */
    public function recordPayment(Request $request, Sale $sale)
    {
        // Force validation
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
        ]);

        try {
            DB::beginTransaction();
            
            // Use the shared logic we created earlier
            $this->executePaymentLogic(
                $sale, 
                $validated['amount'], 
                $validated['payment_date'], 
                $validated['payment_method']
            );
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully!'
            ])
            // UX FIX: Force browser to bypass any local or middleware cache
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Payment AJAX Error: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'error' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function invoice(Sale $sale, Request $request)
    {
        try {
            $sale->load(['customer', 'saleable', 'payments', 'items.saleable']);

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


            /**
         * UX Logic: Fulfills the order by depleting stock from multiple batches (FIFO)
         */
        private function handleInventoryDeduction($sale, $validated)
        {
            $saleableType = ltrim($validated['saleable_type'], '\\');
            $remaining = (int)$validated['quantity'];

            if ($saleableType === Egg::class || $saleableType === 'App\\Models\\Egg') {
                $isCracked = ($validated['product_variant'] === 'cracked');

                // Find batches of this variant, oldest first (FIFO)
                $batches = Egg::where('is_cracked', $isCracked)
                    ->where('crates', '>', 0)
                    ->orderBy('date_laid', 'asc')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {
                    if ($remaining <= 0) break;
                    $take = min($batch->crates, $remaining);

                    // Create the tracking record for this specific batch
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'saleable_type' => Egg::class,
                        'saleable_id' => $batch->id,
                        'quantity' => $take,
                        'unit_price' => $validated['unit_price'],
                        'subtotal' => $take * $validated['unit_price'],
                    ]);

                    $batch->decrement('crates', $take);
                    $remaining -= $take;
                }
            } else {
                // Handle Birds (Standard Single-Batch logic)
                $bird = Bird::lockForUpdate()->find($validated['saleable_id']);
                $bird->decrement('quantity', $remaining);
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'saleable_type' => Bird::class,
                    'saleable_id' => $bird->id,
                    'quantity' => $remaining,
                    'unit_price' => $validated['unit_price'],
                    'subtotal' => $sale->total_amount,
                ]);
            }
        }

        /**
         * Shared logic to handle financial records and logging
         */
        private function logSaleFinancials($sale, $customer)
        {
            Transaction::create([
                'type' => 'sale',
                'amount' => $sale->total_amount,
                'status' => 'pending',
                'date' => $sale->sale_date,
                'source_type' => Sale::class,
                'source_id' => $sale->id,
                'user_id' => Auth::id(),
                'description' => "Sale of {$sale->quantity} to {$customer->name}",
            ]);

            Income::create([
                'source' => "Sale #{$sale->id}",
                'description' => "Sale of {$sale->quantity} to {$customer->name}",
                'amount' => $sale->total_amount,
                'date' => $sale->sale_date,
                'created_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_sale',
                'details' => "Created sale #{$sale->id} for {$customer->name} (Total: ₵ {$sale->total_amount})",
            ]);

            Alert::create([
                'message' => "New sale #{$sale->id} for customer {$customer->name}",
                'type' => 'sale',
                'user_id' => Auth::id(),
            ]);
        }

        /**
         * Shared Private Logic to actually write the payment to DB
         */
        private function executePaymentLogic(Sale $sale, $amount, $date, $method)
        {
            $amount = round((float)$amount, 2);

            Payment::create([
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'amount' => $amount,
                'payment_date' => $date,
                'payment_method' => $method,
                'created_by' => Auth::id(),
            ]);

            Transaction::create([
                'type' => 'payment',
                'amount' => $amount,
                'status' => 'approved',
                'date' => $date,
                'source_type' => Sale::class,
                'source_id' => $sale->id,
                'user_id' => Auth::id(),
                'description' => "Payment for Sale #{$sale->id}",
            ]);

            $sale->refresh();
            $sale->updatePaymentStatus(); 
        }

}