<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Bird;
use App\Models\Chicks;
use App\Models\Customer;
use App\Models\Egg;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Feed;
use App\Models\HealthCheck;
use App\Models\Income;
use App\Models\Instruction;
use App\Models\Inventory;
use App\Models\MedicineLog;
use App\Models\Mortalities;
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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $isAdmin = $user->hasRole('admin');
        $canManageFinances = $user->hasPermissionTo('manage_finances');
        $canViewSales = $user->hasPermissionTo('view-sales');

        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());
        $period = [$start, $end];

        $chicks = Chicks::sum('quantity_bought') ?? 0;
        $layers = Bird::where('type', 'layer')->sum('quantity') ?? 0;
        $broilers = Bird::where('type', 'broiler')->sum('quantity') ?? 0;
        $totalBirds = $chicks + $layers + $broilers;

        $metrics = [
            'egg_crates' => Egg::whereBetween('date_laid', $period)->sum('crates') ?? 0,
            'feed_kg' => Feed::whereBetween('purchase_date', $period)->sum('quantity') ?? 0,
            'mortality' => Mortalities::whereBetween('date', $period)->sum('quantity') ?? 0,
            'medicine_buy' => MedicineLog::where('type', 'purchase')
                ->whereBetween('date', $period)
                ->sum('quantity') ?? 0,
            'medicine_use' => MedicineLog::where('type', 'consumption')
                ->whereBetween('date', $period)
                ->sum('quantity') ?? 0,
            'sales' => 0,
            'customers' => 0,
        ];

        $mortalityRate = $totalBirds ? ($metrics['mortality'] / $totalBirds) * 100 : 0;
        $fcr = $metrics['egg_crates'] ? round($metrics['feed_kg'] / $metrics['egg_crates'], 2) : 0;

        $eggTrend = Egg::whereBetween('date_laid', $period)
            ->select(DB::raw('DATE(date_laid) as date'), DB::raw('SUM(crates) as value'))
            ->groupBy('date_laid')
            ->orderBy('date_laid')
            ->take(50)
            ->get();

        $feedTrend = Feed::whereBetween('purchase_date', $period)
            ->select(DB::raw('DATE(purchase_date) as date'), DB::raw('SUM(quantity) as value'))
            ->groupBy('purchase_date')
            ->orderBy('purchase_date')
            ->take(50)
            ->get();

        $recentActivities = collect();
        if ($isAdmin) {
            $recentActivities = UserActivityLog::whereBetween('created_at', $period)
                ->select('action', 'user_id', 'created_at')
                ->take(5)
                ->get()
                ->map(function ($activity) {
                    $user = User::find($activity->user_id);
                    $activity->user_name = $user ? $user->name : 'System';
                    return $activity;
                });
        } else {
            $recentActivities = collect()
                ->concat(Sale::whereBetween('sale_date', $period)
                    ->select(DB::raw("'Sale added' as action"), 'created_by as user_id', 'sale_date as created_at')
                    ->take(5)
                    ->get())
                ->concat(Expense::whereBetween('date', $period)
                    ->select(DB::raw("'Expense logged' as action"), 'created_by as user_id', 'date as created_at')
                    ->take(5)
                    ->get())
                ->concat(Egg::whereBetween('date_laid', $period)
                    ->select(DB::raw("'Egg production recorded' as action"), 'created_by as user_id', 'date_laid as created_at')
                    ->take(5)
                    ->get())
                ->map(function ($activity) {
                    $user = User::find($activity->user_id);
                    $activity->user_name = $user ? $user->name : 'System';
                    return $activity;
                })
                ->filter(function ($activity) {
                    return !str_contains($activity->action, 'System');
                })
                ->sortByDesc('created_at')
                ->take(5);
        }

        $dailyInstructions = collect([]);
        if ($user->hasRole('labourer')) {
            $dailyInstructions = Instruction::whereDate('date', now())
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(['id', 'content', 'created_at']);
        }

        $incomeData = [];
        $incomeLabels = [];
        for ($i = 0; $i < 6; $i++) {
            $month = now()->subMonths($i);
            $incomeLabels[] = $month->format('M Y');
            $incomeData[] = Income::whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->sum('amount') ?? 0;
        }
        $incomeLabels = array_reverse($incomeLabels);
        $incomeData = array_reverse($incomeData);

        $totalExpenses = 0;
        $totalIncome = 0;
        $profit = 0;
        $employees = 0;
        $payroll = 0;
        $salesTrend = collect([]);
        $expenseTrend = collect([]);
        $incomeTrend = collect([]);
        $profitTrend = collect([]);
        $invoiceStatuses = [
            'pending' => 0,
            'paid' => 0,
            'partially_paid' => 0,
            'overdue' => 0,
        ];
        $alerts = collect([]);
        $healthSummary = collect([]);
        $vaccinationSchedule = collect([]);
        $suppliers = collect([]);
        $payrollStatus = collect([]);
        $pendingApprovals = collect([]);

        if ($isAdmin || $canManageFinances) {
            $totalExpenses = Expense::whereBetween('date', $period)->sum('amount') ?? 0;
            $totalIncome = Income::whereBetween('date', $period)->sum('amount') ?? 0;
            $profit = $totalIncome - $totalExpenses;

            $expenseTrend = Expense::whereBetween('date', $period)
                ->select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as value'))
                ->groupBy('date')
                ->orderBy('date')
                ->take(50)
                ->get();

            $incomeTrend = Income::whereBetween('date', $period)
                ->select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as value'))
                ->groupBy('date')
                ->orderBy('date')
                ->take(50)
                ->get();

            $profitTrend = DB::table('income')
                ->select(DB::raw('income.date as date'), DB::raw('SUM(income.amount - COALESCE(expenses.amount, 0)) as value'))
                ->leftJoin('expenses', 'income.date', '=', 'expenses.date')
                ->whereBetween('income.date', $period)
                ->groupBy('income.date')
                ->orderBy('income.date')
                ->take(50)
                ->get();
        }

        if ($isAdmin || $canViewSales) {
            $metrics['sales'] = Sale::whereBetween('sale_date', $period)
                ->selectRaw('SUM(total_amount) as total')
                ->value('total') ?? 0;
            $metrics['customers'] = Customer::count() ?? 0;

            $salesTrend = Sale::whereBetween('sale_date', $period)
                ->select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as value'))
                ->groupBy('date')
                ->orderBy('date')
                ->take(50)
                ->get();

            $invoiceStatuses = [
                'pending' => Sale::where('status', 'pending')->count(),
                'paid' => Sale::where('status', 'paid')->count(),
                'partially_paid' => Sale::where('status', 'partially_paid')->count(),
                'overdue' => Sale::where('status', 'overdue')->count(),
            ];
        }

        if ($isAdmin) {
            $alerts = Alert::whereBetween('created_at', $period)
                ->get()
                ->concat(Cache::remember('low_stock_alerts', 3600, function () use ($period) {
                    $lowStockAlerts = collect();

                    $lowInventory = Inventory::where('qty', '<', DB::raw('threshold'))
                        ->whereBetween('updated_at', $period)
                        ->get()
                        ->map(function ($item) {
                            return new Alert([
                                'id' => (string) Str::uuid(),
                                'message' => "Low stock for " . ($item->name ?? 'Unknown Item') . ": {$item->qty} remaining (Threshold: {$item->threshold})",
                                'type' => 'warning',
                                'created_at' => now(),
                                'user_id' => null,
                            ]);
                        });
                    $lowStockAlerts = $lowStockAlerts->concat($lowInventory);

                    $lowFeed = Feed::where('quantity', '<', DB::raw('threshold'))
                        ->whereBetween('purchase_date', $period)
                        ->get()
                        ->map(function ($item) {
                            return new Alert([
                                'id' => (string) Str::uuid(),
                                'message' => "Low feed stock for " . ($item->name ?? 'Unknown Feed') . ": {$item->quantity} kg remaining (Threshold: {$item->threshold} kg)",
                                'type' => 'warning',
                                'created_at' => now(),
                                'user_id' => null,
                            ]);
                        });
                    $lowStockAlerts = $lowStockAlerts->concat($lowFeed);

                    // Skip medicine alerts due to negative quantities issue
                    /*
                    $lowMedicine = MedicineLog::select('medicine_name')
                        ->selectRaw('SUM(CASE WHEN type = "purchase" THEN quantity ELSE -quantity END) as net_quantity')
                        ->whereBetween('date', $period)
                        ->groupBy('medicine_name')
                        ->havingRaw('net_quantity < ?', [10])
                        ->get()
                        ->map(function ($item) {
                            return new Alert([
                                'id' => (string) Str::uuid(),
                                'message' => "Low medicine stock for " . ($item->medicine_name ?? 'Unknown Medicine') . ": {$item->net_quantity} units remaining (Threshold: 10 units)",
                                'type' => 'warning',
                                'created_at' => now(),
                                'user_id' => null,
                            ]);
                        });
                    $lowStockAlerts = $lowStockAlerts->concat($lowMedicine);
                    */

                    return $lowStockAlerts;
                }));
        } else {
            $alerts = Alert::where('user_id', $user->id)
                ->whereNull('read_at')
                ->whereBetween('created_at', $period)
                ->take(50)
                ->get();
        }

        if ($isAdmin || $canManageFinances) {
            $pendingApprovals = Transaction::with('source')
                ->where('status', 'pending')
                ->whereBetween('date', $period)
                ->orderBy('date', 'desc')
                ->take(5)
                ->get(['id', 'type', 'amount', 'date', 'source_type', 'source_id']);
        }

        if ($user->hasRole('farm_manager')) {
            $healthSummary = HealthCheck::whereBetween('check_date', $period)
                ->select(DB::raw('check_date as date'), DB::raw('COUNT(*) as checks'), DB::raw('SUM(CASE WHEN status = "unhealthy" THEN 1 ELSE 0 END) as unhealthy'))
                ->groupBy('check_date')
                ->orderBy('check_date', 'desc')
                ->take(5)
                ->get();
        }

        if ($user->hasRole('veterinarian')) {
            $vaccinationSchedule = VaccinationLog::where('status', 'pending')
                ->whereDate('due_date', '>=', now())
                ->orderBy('due_date')
                ->take(5)
                ->get(['id', 'vaccine_name', 'due_date']);
        }

        if ($user->hasRole('inventory_manager')) {
            $suppliers = Supplier::orderBy('name')
                ->take(5)
                ->get(['id', 'name', 'contact_info']);
        }

        if ($user->hasRole('accountant')) {
            $payrollStatus = Payroll::whereBetween('pay_date', $period)
                ->select(DB::raw('pay_date as date'), DB::raw('COUNT(*) as employees'), DB::raw('SUM(net_pay) as total'))
                ->groupBy('pay_date')
                ->orderBy('pay_date', 'desc')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    $item->date = Carbon::parse($item->date);
                    return $item;
                });
        }

        return view('dashboard.index', compact(
            'start', 'end', 'totalExpenses', 'totalIncome', 'profit', 'chicks', 'layers',
            'broilers', 'metrics', 'mortalityRate', 'fcr', 'employees', 'payroll',
            'eggTrend', 'feedTrend', 'salesTrend', 'alerts', 'invoiceStatuses',
            'recentActivities', 'expenseTrend', 'incomeTrend', 'profitTrend',
            'pendingApprovals', 'healthSummary', 'vaccinationSchedule', 'suppliers',
            'dailyInstructions', 'payrollStatus', 'incomeLabels', 'incomeData'
        ));
    }

    public function export(Request $request)
    {
        /** @var User $user */
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
            ['Chicks', Chicks::sum('quantity_bought') ?? 0],
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
        /** @var User $user */
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
        $totalBirds = (Chicks::sum('quantity_bought') ?? 0) +
                      (Bird::where('type', 'layer')->sum('quantity') ?? 0) +
                      (Bird::where('type', 'broiler')->sum('quantity') ?? 0);
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