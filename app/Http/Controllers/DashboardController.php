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
use App\Models\Payroll;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());

        // Fetch paginated alerts
        $alerts = Alert::where('user_id', $user->id)
            ->where('is_read', false)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Dashboard data
        $totalBirds = Bird::whereNull('deleted_at')->sum('quantity') ?? 0;
        $layers = Bird::where('type', 'layer')->where('stage', '!=', 'chick')->whereNull('deleted_at')->sum('quantity') ?? 0;
        $broilers = Bird::where('type', 'broiler')->where('stage', '!=', 'chick')->whereNull('deleted_at')->sum('quantity') ?? 0;
        $chicks = Bird::where('stage', 'chick')->sum('quantity_bought') ?? 0;
        $eggCrates = Egg::whereBetween('date_laid', [$start, $end])->whereNull('deleted_at')->sum('crates');
        $feedQuantity = Feed::whereBetween('purchase_date', [$start, $end])->whereNull('deleted_at')->sum('quantity');
        $mortalities = Mortalities::whereBetween('date', [$start, $end])->whereNull('deleted_at')->sum('quantity');
        $medicinePurchased = MedicineLog::where('type', 'purchase')
            ->whereBetween('date', [$start, $end])
            ->whereNull('deleted_at')
            ->sum('quantity');
        $medicineConsumed = MedicineLog::where('type', 'consumption')
            ->whereBetween('date', [$start, $end])
            ->whereNull('deleted_at')
            ->sum('quantity');
        $totalIncome = Income::whereBetween('date', [$start, $end])->whereNull('deleted_at')->sum('amount');
        $totalExpenses = Expense::whereBetween('date', [$start, $end])->whereNull('deleted_at')->sum('amount');
        $totalSales = Sale::whereBetween('sale_date', [$start, $end])->whereNull('deleted_at')->sum('total_amount');
        $customerCount = Customer::whereNull('deleted_at')->count();
        $pendingSales = Sale::where('status', 'pending')->whereNull('deleted_at')->count();
        $paidSales = Sale::where('status', 'paid')->whereNull('deleted_at')->count();
        $partiallyPaidSales = Sale::where('status', 'partially_paid')->whereNull('deleted_at')->count();
        $overdueSales = Sale::where('status', 'overdue')->whereNull('deleted_at')->count();

        // Monthly income for the last 6 months
        $monthlyIncome = [];
        for ($i = 0; $i < 6; $i++) {
            $month = now()->subMonths($i);
            $monthlyIncome[] = Income::whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->whereNull('deleted_at')
                ->sum('amount');
        }

        // Egg production data
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

        // Feed consumption data
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

        // Recent activity logs
        $recentActivities = UserActivityLog::select('action', 'user_id', 'created_at')
            ->whereBetween('created_at', [$start, $end])
            ->whereNull('deleted_at')
            ->limit(5)
            ->get()->map(function ($log) {
                $log->user_name = User::find($log->user_id)->name ?? 'Unknown';
                return $log;
            });

        // Expense data
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

        // Income data
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

        // Net financial data
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

        // Sales data
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
        $payroll = Payroll::whereBetween('pay_date', [$start, $end])
            ->whereNull('deleted_at')
            ->sum('net_pay');
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
        $suppliers = collect();

        return view('dashboard.index', compact(
            'alerts',
            'totalBirds',
            'layers',
            'broilers',
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
            'suppliers'
        ));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

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
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
        exit;
    }

    public function exportPDF(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        $data = $this->index($request)->getData();
        $pdf = Pdf::loadView('dashboard_pdf', $data);
        return $pdf->download('dashboard_report_' . now()->format('Ymd') . '.pdf');
    }

    private function calculateMortalityRate($start, $end)
    {
        $totalBirds = Bird::sum('quantity') ?? 0;
        $mortality = Mortalities::whereBetween('date', [$start, $end])->sum('quantity') ?? 0;
        return $totalBirds ? ($mortality / $totalBirds) * 100 : 0;
    }

    private function calculateFCR($start, $end)
    {
        $eggCrates = Egg::whereBetween('date_laid', [$start, $end])->sum('crates') ?? 0;
        $feedKg = Feed::whereBetween('purchase_date', [$start, $end])->sum('quantity') ?? 0;
        return $eggCrates ? round($feedKg / $eggCrates, 2) : 0;
    }
}