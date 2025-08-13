<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Bird;
use App\Models\Customer;
use App\Models\Egg;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Feed;
use App\Models\HealthCheck;
use App\Models\Income;
use App\Models\MedicineLog;
use App\Models\Mortalities;
use App\Models\Order;
use App\Models\Payroll;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\VaccinationLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doNotCacheResponse')->only('index'); 
    }

    
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                Log::warning('Unauthorized access attempt to dashboard');
                return redirect()->route('login')->with('error', 'Please log in to access the dashboard.');
            }

            // Validate date inputs
            try {
                $request->validate([
                    'start_date' => 'nullable|date|before_or_equal:end_date',
                    'end_date' => 'nullable|date|after_or_equal:start_date',
                ]);
            } catch (ValidationException $e) {
                Log::warning('Invalid date range input', ['errors' => $e->errors()]);
                return back()->withErrors($e->errors())->withInput();
            }

            $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());

            // Cache key for dashboard data
            $cacheKey = "dashboard_data_{$user->id}_{$start}_{$end}";
            $cacheTTL = 300; // Cache for 5 minutes

            // Fetch data from cache or database
            $dashboardData = Cache::remember($cacheKey, $cacheTTL, function () use ($start, $end) {
                // Fetch paginated alerts
                $alerts = Alert::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->whereBetween('created_at', [$start, $end])
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

                // Dashboard metrics
                $totalBirds = Bird::whereNull('deleted_at')->sum('quantity') ?? 0;
                $layerBirds = Bird::where('type', 'layer')
                    ->where('stage', '!=', 'chick')
                    ->whereNull('deleted_at')
                    ->sum('quantity') ?? 0;
                $broilerBirds = Bird::where('type', 'broiler')
                    ->where('stage', '!=', 'chick')
                    ->whereNull('deleted_at')
                    ->sum('quantity') ?? 0;
                $chicks = Bird::where('stage', 'chick')
                    ->whereNull('deleted_at')
                    ->sum('quantity_bought') ?? 0;
                $eggCrates = Egg::whereBetween('date_laid', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('crates') ?? 0;
                $feedQuantity = Feed::whereBetween('purchase_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('quantity') ?? 0;
                $mortalities = Mortalities::whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('quantity') ?? 0;
                $medicinePurchased = MedicineLog::where('type', 'purchase')
                    ->whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('quantity') ?? 0;
                $medicineConsumed = MedicineLog::where('type', 'consumption')
                    ->whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('quantity') ?? 0;
                $totalIncome = Income::whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0;
                $totalExpenses = Expense::whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0;
                $totalSales = Sale::whereBetween('sale_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('total_amount') ?? 0;
                $customerCount = Customer::whereNull('deleted_at')->count();
                $pendingSales = Sale::where('status', 'pending')
                    ->whereNull('deleted_at')
                    ->count();
                $paidSales = Sale::where('status', 'paid')
                    ->whereNull('deleted_at')
                    ->count();
                $partiallyPaidSales = Sale::where('status', 'partially_paid')
                    ->whereNull('deleted_at')
                    ->count();
                $overdueSales = Sale::where('status', 'overdue')
                    ->whereNull('deleted_at')
                    ->count();
                $activeSuppliers = Supplier::whereNull('deleted_at')->count();
                $pendingOrders = Order::where('status', 'pending')
                    ->whereBetween('created_at', [$start, $end])
                    ->whereNull('deleted_at')
                    ->count();
                $totalOrderAmount = Order::whereBetween('created_at', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('total_amount') ?? 0;
                $pendingPayrolls = Payroll::where('status', 'pending')
                    ->whereBetween('pay_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->count();
                $totalPayroll = Payroll::whereBetween('pay_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('net_pay') ?? 0;
                $upcomingVaccinations = VaccinationLog::where('next_vaccination_date', '<=', now()->addDays(7))
                    ->where('next_vaccination_date', '>=', now())
                    ->whereNull('deleted_at')
                    ->with('bird')
                    ->count();
                $pendingTransactions = Transaction::where('status', 'pending')
                    ->whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->count();
                $totalTransactionAmount = Transaction::whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0;

                // Additional data from other controllers
                $vaccinationLogs = VaccinationLog::with('bird')
                    ->whereBetween('date_administered', [$start, $end])
                    ->whereNull('deleted_at')
                    ->orderBy('date_administered', 'desc')
                    ->take(5)
                    ->get();
                $recentSales = Sale::with('customer', 'saleable')
                    ->whereBetween('sale_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->orderBy('sale_date', 'desc')
                    ->take(5)
                    ->get();
                $eggSales = Sale::where('saleable_type', Egg::class)
                    ->whereBetween('sale_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('total_amount') ?? 0;
                $birdSales = Sale::where('saleable_type', Bird::class)
                    ->whereBetween('sale_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('total_amount') ?? 0;
                $recentMortalities = Mortalities::with('bird')
                    ->whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->orderBy('date', 'desc')
                    ->take(5)
                    ->get();

                // Monthly income for the last 6 months
                $monthlyIncome = [];
                for ($i = 0; $i < 6; $i++) {
                    $month = now()->subMonths($i);
                    $monthlyIncome[] = Income::whereMonth('date', $month->month)
                        ->whereYear('date', $month->year)
                        ->whereNull('deleted_at')
                        ->sum('amount') ?? 0;
                }

                // Chart data with limits to prevent overload
                $eggProduction = Egg::select(
                    DB::raw('DATE(date_laid) as date'),
                    DB::raw('SUM(crates) as value')
                )
                    ->whereBetween('date_laid', [$start, $end])
                    ->whereNull('deleted_at')
                    ->groupBy('date_laid')
                    ->orderBy('date_laid', 'asc')
                    ->limit(50)
                    ->get();

                $feedConsumption = Feed::select(
                    DB::raw('DATE(purchase_date) as date'),
                    DB::raw('SUM(quantity) as value')
                )
                    ->whereBetween('purchase_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->groupBy('purchase_date')
                    ->orderBy('purchase_date', 'asc')
                    ->limit(50)
                    ->get();

                $expenseData = Expense::select(
                    DB::raw('DATE(date) as date'),
                    DB::raw('SUM(amount) as value')
                )
                    ->whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->limit(50)
                    ->get();

                $incomeData = Income::select(
                    DB::raw('DATE(date) as date'),
                    DB::raw('SUM(amount) as value')
                )
                    ->whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->limit(50)
                    ->get();

                $netFinancialData = Income::select(
                    DB::raw('DATE(income.date) as date'),
                    DB::raw('SUM(income.amount - COALESCE(expenses.amount, 0)) as value')
                )
                    ->leftJoin('expenses', 'income.date', '=', 'expenses.date')
                    ->whereBetween('income.date', [$start, $end])
                    ->whereNull('income.deleted_at')
                    ->groupBy('income.date')
                    ->orderBy('income.date', 'asc')
                    ->limit(50)
                    ->get();

                $salesData = Sale::select(
                    DB::raw('DATE(sale_date) as date'),
                    DB::raw('SUM(total_amount) as value')
                )
                    ->whereBetween('sale_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->groupBy('sale_date')
                    ->orderBy('sale_date', 'asc')
                    ->limit(50)
                    ->get();

                // Recent activity logs
                $recentActivities = UserActivityLog::select('action', 'user_id', 'created_at')
                    ->whereBetween('created_at', [$start, $end])
                    ->whereNull('deleted_at')
                    ->limit(5)
                    ->get()
                    ->map(function ($log) {
                        $log->user_name = User::find($log->user_id)->name ?? 'Unknown';
                        return $log;
                    });

                // Pending approvals
                $pendingApprovals = Transaction::where('status', 'pending')
                    ->whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->with('source')
                    ->get();

                // KPI variables
                $metrics = [
                    'egg_crates' => $eggCrates,
                    'feed_kg' => $feedQuantity,
                    'mortality' => $mortalities,
                    'sales' => $totalSales,
                    'customers' => $customerCount,
                    'medicine_buy' => $medicinePurchased,
                    'medicine_use' => $medicineConsumed,
                ];
                $mortalityRate = ($totalBirds > 0) ? ($mortalities / $totalBirds) * 100 : 0;
                $fcr = ($eggCrates > 0 && $feedQuantity > 0) ? $feedQuantity / $eggCrates : 0;
                $employees = Employee::whereNull('deleted_at')->count();
                $eggTrend = $eggProduction;
                $feedTrend = $feedConsumption;
                $salesTrend = $salesData;
                $incomeLabels = $incomeData->pluck('date')->toArray();
                $incomeTrend = $incomeData;
                $expenseTrend = $expenseData;
                $profitTrend = $netFinancialData;
                $profit = $totalIncome - $totalExpenses;
                $payrollStatus = Payroll::select(
                    DB::raw('DATE(pay_date) as date'),
                    DB::raw('COUNT(DISTINCT employee_id) as employees'),
                    DB::raw('SUM(net_pay) as total'),
                    'status'
                )
                    ->whereBetween('pay_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->groupBy('pay_date', 'status')
                    ->paginate(10);
                $invoiceStatuses = [
                    'pending' => $pendingSales,
                    'paid' => $paidSales,
                    'partially_paid' => $partiallyPaidSales,
                    'overdue' => $overdueSales,
                ];

                // Role-specific variables
                $dailyInstructions = collect();
                $healthSummary = collect();
                $vaccinationSchedule = collect();
                $suppliers = Supplier::whereNull('deleted_at')
                    ->orderBy('name')
                    ->take(5)
                    ->get();

                return compact(
                    'alerts',
                    'totalBirds',
                    'layerBirds',
                    'broilerBirds',
                    'chicks',
                    'eggCrates',
                    'feedQuantity',
                    'mortalities',
                    'medicinePurchased',
                    'medicineConsumed',
                    'totalIncome',
                    'totalExpenses',
                    'totalSales',
                    'customerCount',
                    'pendingSales',
                    'paidSales',
                    'partiallyPaidSales',
                    'overdueSales',
                    'monthlyIncome',
                    'eggProduction',
                    'feedConsumption',
                    'recentActivities',
                    'expenseData',
                    'incomeData',
                    'netFinancialData',
                    'salesData',
                    'pendingApprovals',
                    'metrics',
                    'mortalityRate',
                    'fcr',
                    'employees',
                    'payroll',
                    'eggTrend',
                    'feedTrend',
                    'salesTrend',
                    'incomeLabels',
                    'incomeTrend',
                    'expenseTrend',
                    'profitTrend',
                    'profit',
                    'payrollStatus',
                    'invoiceStatuses',
                    'dailyInstructions',
                    'healthSummary',
                    'vaccinationSchedule',
                    'suppliers',
                    'vaccinationLogs',
                    'upcomingVaccinations',
                    'pendingTransactions',
                    'totalTransactionAmount',
                    'activeSuppliers',
                    'pendingOrders',
                    'totalOrderAmount',
                    'totalPayroll',
                    'pendingPayrolls',
                    'recentSales',
                    'eggSales',
                    'birdSales',
                    'recentMortalities'
                );
            });

            return view('dashboard.index', $dashboardData);
        } catch (\Exception $e) {
            Log::error('Dashboard index error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'An error occurred while loading the dashboard. Please try again later.');
        }
    }

    public function export(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->hasRole('admin')) {
                Log::warning('Unauthorized export attempt', ['user_id' => $user->id ?? null]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $request->validate([
                'start_date' => 'nullable|date|before_or_equal:end_date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $start = $request->input('start_date', now()->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());

            $data = [
                ['Metric', 'Value'],
                ['Total Expenses', number_format(Expense::whereBetween('date', [$start, $end])->sum('amount') ?? 0, 2)],
                ['Total Income', number_format(Income::whereBetween('date', [$start, $end])->sum('amount') ?? 0, 2)],
                ['Profit', number_format(Income::whereBetween('date', [$start, $end])->sum('amount') - Expense::whereBetween('date', [$start, $end])->sum('amount'), 2)],
                ['Egg Crates', Egg::whereBetween('date_laid', [$start, $end])->sum('crates') ?? 0],
                ['Feed (kg)', Feed::whereBetween('purchase_date', [$start, $end])->sum('quantity') ?? 0],
                ['Total Birds', Bird::sum('quantity') ?? 0],
                ['Layers', Bird::where('type', 'layer')->sum('quantity') ?? 0],
                ['Broilers', Bird::where('type', 'broiler')->sum('quantity') ?? 0],
                ['Mortality Rate (%)', number_format($this->calculateMortalityRate($start, $end), 2)],
                ['FCR', number_format($this->calculateFCR($start, $end), 2)],
                ['Employees', Employee::count() ?? 0],
                ['Payroll', number_format(Payroll::whereBetween('pay_date', [$start, $end])->sum('net_pay') ?? 0, 2)],
                ['Sales', number_format(Sale::whereBetween('sale_date', [$start, $end])->sum('total_amount') ?? 0, 2)],
                ['Customers', Customer::count() ?? 0],
                ['Medicine Bought', MedicineLog::where('type', 'purchase')->whereBetween('date', [$start, $end])->sum('quantity') ?? 0],
                ['Medicine Used', MedicineLog::where('type', 'consumption')->whereBetween('date', [$start, $end])->sum('quantity') ?? 0],
            ];

            $filename = "dashboard_export_" . now()->format('Ymd_His') . ".csv";
            $handle = fopen('php://output', 'w');
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            fputs($handle, "\xEF\xBB\xBF"); // Add UTF-8 BOM for Excel compatibility
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
            exit;
        } catch (\Exception $e) {
            Log::error('Export error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to export data. Please try again.');
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->hasRole('admin')) {
                Log::warning('Unauthorized PDF export attempt', ['user_id' => $user->id ?? null]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $data = $this->index($request)->getData();
            $pdf = Pdf::loadView('dashboard_pdf', $data);
            return $pdf->download('dashboard_report_' . now()->format('Ymd') . '.pdf');
        } catch (\Exception $e) {
            Log::error('PDF export error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to generate PDF. Please try again.');
        }
    }

    private function calculateMortalityRate($start, $end)
    {
        try {
            $totalBirds = Bird::sum('quantity') ?? 0;
            $mortality = Mortalities::whereBetween('date', [$start, $end])->sum('quantity') ?? 0;
            return $totalBirds ? round(($mortality / $totalBirds) * 100, 2) : 0;
        } catch (\Exception $e) {
            Log::error('Mortality rate calculation error', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    private function calculateFCR($start, $end)
    {
        try {
            $eggCrates = Egg::whereBetween('date_laid', [$start, $end])->sum('crates') ?? 0;
            $feedKg = Feed::whereBetween('purchase_date', [$start, $end])->sum('quantity') ?? 0;
            return $eggCrates ? round($feedKg / $eggCrates, 2) : 0;
        } catch (\Exception $e) {
            Log::error('FCR calculation error', ['error' => $e->getMessage()]);
            return 0;
        }
    }
}