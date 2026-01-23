<?php

namespace App\Http\Controllers;

use App\Models\Bird;
use App\Models\Customer;
use App\Models\Egg;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Feed;
use App\Models\Income;
use App\Models\MedicineLog;
use App\Models\Mortalities;
use App\Models\Payroll;
use App\Models\Sale;
use App\Models\Reminder;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PDF; // Assuming a PDF wrapper is used

class DashboardController extends Controller
{
    protected WeatherService $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->middleware('auth');
        $this->middleware('doNotCacheResponse')->only('dashboard.index');

        $this->weatherService = $weatherService;
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please log in to access the dashboard.');
            }

            // Validate input dates
            try {
                $request->validate([
                    'start_date' => 'nullable|date|before_or_equal:end_date',
                    'end_date'   => 'nullable|date|after_or_equal:start_date',
                ]);
            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            }

            $start = $request->input('start_date', now()->subMonths(1)->startOfMonth()->toDateString());
            $end   = $request->input('end_date', now()->endOfMonth()->toDateString());

            $cacheKey = "dashboard_data_lite_{$user->id}_{$start}_{$end}";
            $cacheTTL = 300; // 5 minutes

            // Collect only necessary dashboard variables
            $dashboardData = Cache::remember($cacheKey, $cacheTTL, function () use ($start, $end) {
                
                // 1. Task Calendar (Reminders)
                $reminders = Reminder::where('is_done', false)
                    ->orderBy('severity', 'desc')
                    ->latest()
                    ->take(5)
                    ->get();

                // 2. Financial Summary (Totals)
                $totalIncome   = Income::whereBetween('date', [$start, $end])->whereNull('deleted_at')->sum('amount') ?? 0;
                $totalExpenses = Expense::whereBetween('date', [$start, $end])->whereNull('deleted_at')->sum('amount') ?? 0;
                $profit        = $totalIncome - $totalExpenses;

                // 3. Financial Summary (Mini-Charts Trends)
                // Expense Trend
                $expenseData = Expense::select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as value'))
                    ->whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->groupBy(DB::raw('DATE(date)'))
                    ->orderBy('date', 'asc')
                    ->limit(50)
                    ->get();

                // Income Trend
                $incomeData = Income::select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as value'))
                    ->whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->groupBy(DB::raw('DATE(date)'))
                    ->orderBy('date', 'asc')
                    ->limit(50)
                    ->get();

                // Profit Trend (Calculated)
                $incomeSums = Income::select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as income'))
                    ->whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->groupBy(DB::raw('DATE(date)'))
                    ->get()
                    ->keyBy('date');

                $expenseSums = Expense::select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as expense'))
                    ->whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->groupBy(DB::raw('DATE(date)'))
                    ->get()
                    ->keyBy('date');

                $dates = $incomeSums->keys()->merge($expenseSums->keys())->unique()->sort();
                $profitTrend = $dates->map(function ($date) use ($incomeSums, $expenseSums) {
                    $inc = $incomeSums->get($date)->income ?? 0;
                    $exp = $expenseSums->get($date)->expense ?? 0;
                    return (object)['date' => $date, 'value' => $inc - $exp];
                })->values(); // Reset keys for JSON

                // 4. KPIs - Flock Statistics
                $totalBirds   = Bird::whereNull('deleted_at')->sum('quantity') ?? 0;
                $layerBirds   = Bird::where('type', 'layer')->whereNull('deleted_at')
                                    ->sum(DB::raw('CASE WHEN stage = "chick" THEN alive ELSE quantity END')) ?? 0;
                $broilerBirds = Bird::where('type', 'broiler')->whereNull('deleted_at')
                                    ->sum(DB::raw('CASE WHEN stage = "chick" THEN alive ELSE quantity END')) ?? 0;
                $chicks       = Bird::where('stage', 'chick')->whereNull('deleted_at')->sum('quantity_bought') ?? 0;
                $mortalities  = Mortalities::whereBetween('date', [$start, $end])->whereNull('deleted_at')->sum('quantity') ?? 0;
                $mortalityRate = ($totalBirds > 0) ? ($mortalities / $totalBirds) * 100 : 0;

                // 5. KPIs - Production
                $eggCrates    = Egg::whereBetween('date_laid', [$start, $end])->whereNull('deleted_at')->sum('crates') ?? 0;
                $feedQuantity = Feed::whereBetween('purchase_date', [$start, $end])->whereNull('deleted_at')->sum('quantity') ?? 0;
                $fcr          = ($eggCrates > 0 && $feedQuantity > 0) ? $feedQuantity / $eggCrates : 0;

                // 6. KPIs - Operations
                $employees        = Employee::whereNull('deleted_at')->count();
                $totalPayroll     = Payroll::whereBetween('pay_date', [$start, $end])->whereNull('deleted_at')->sum('net_pay') ?? 0;
                $totalSales       = Sale::whereBetween('sale_date', [$start, $end])->whereNull('deleted_at')->sum('total_amount') ?? 0;
                $customerCount    = Customer::whereNull('deleted_at')->count();
                $medicinePurchased= MedicineLog::where('type', 'purchase')->whereBetween('date', [$start, $end])->whereNull('deleted_at')->sum('quantity') ?? 0;
                $medicineConsumed = MedicineLog::where('type', 'consumption')->whereBetween('date', [$start, $end])->whereNull('deleted_at')->sum('quantity') ?? 0;

                // Metric Array for View
                $metrics = [
                    'egg_crates'   => $eggCrates,
                    'feed_kg'      => $feedQuantity,
                    'sales'        => $totalSales,
                    'customers'    => $customerCount,
                    'medicine_buy' => $medicinePurchased,
                    'medicine_use' => $medicineConsumed,
                ];

                return [
                    'reminders'     => $reminders,
                    // Financials
                    'totalIncome'   => $totalIncome,
                    'totalExpenses' => $totalExpenses,
                    'profit'        => $profit,
                    'expenseData'   => $expenseData, // For chart
                    'incomeData'    => $incomeData,  // For chart
                    'profitTrend'   => $profitTrend, // For chart
                    'expenseTrend'  => $expenseData, // Fallback alias
                    'incomeTrend'   => $incomeData,  // Fallback alias
                    // KPIs
                    'totalBirds'    => $totalBirds,
                    'layerBirds'    => $layerBirds,
                    'broilerBirds'  => $broilerBirds,
                    'chicks'        => $chicks,
                    'mortalityRate' => $mortalityRate,
                    'fcr'           => $fcr,
                    'employees'     => $employees,
                    'totalPayroll'  => $totalPayroll,
                    'metrics'       => $metrics,
                    // Dates
                    'start'         => $start,
                    'end'           => $end,
                ];
            });

            // WEATHER: Fetch fresh
            $lat = $request->input('lat');
            $lon = $request->input('lon');
            $defaultLocation = config('app.weather_location', 'Kasoa,GH');

            try {
                if (!empty($lat) && !empty($lon)) {
                    $weather = $this->weatherService->getWeather(['lat' => $lat, 'lon' => $lon]);
                } else {
                    $weather = $this->weatherService->getWeather($defaultLocation);
                }
            } catch (\Throwable $e) {
                Log::warning('Weather fetch failed: ' . $e->getMessage());
                $weather = ['ok' => false, 'message' => 'Weather fetch error'];
            }

            if (!is_array($weather)) {
                $weather = ['ok' => false, 'message' => 'Invalid weather response'];
            }

            $dashboardData['weather'] = $weather;

            return view('dashboard.index', $dashboardData);

        } catch (\Throwable $e) {
            Log::error('Dashboard index error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return view('dashboard.index', [
                'weather'       => ['ok' => false, 'message' => 'Dashboard error'],
                'reminders'     => collect(),
                'expenseData'   => collect(),
                'incomeData'    => collect(),
                'profitTrend'   => collect(),
                'metrics'       => [],
                'totalExpenses' => 0,
                'totalIncome'   => 0,
                'profit'        => 0
            ])->with('error', 'An error occurred while loading the dashboard.');
        }
    }

    /**
     * CSV Export - Independent query to ensure full data availability if needed
     */
    public function export(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $start = $request->input('start_date', now()->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());

            // Re-query scalar values for export
            $expenses = Expense::whereBetween('date', [$start, $end])->whereNull('deleted_at')->sum('amount') ?? 0;
            $income = Income::whereBetween('date', [$start, $end])->whereNull('deleted_at')->sum('amount') ?? 0;
            $profit = $income - $expenses;
            
            // Re-calculate complex KPIs
            $totalBirds = Bird::whereNull('deleted_at')->sum('quantity') ?? 0;
            $mortality = Mortalities::whereBetween('date', [$start, $end])->sum('quantity') ?? 0;
            $mortalityRate = $totalBirds ? round(($mortality / $totalBirds) * 100, 2) : 0;

            $eggCrates = Egg::whereBetween('date_laid', [$start, $end])->sum('crates') ?? 0;
            $feedKg = Feed::whereBetween('purchase_date', [$start, $end])->sum('quantity') ?? 0;
            $fcr = $eggCrates ? round($feedKg / $eggCrates, 2) : 0;

            $data = [
                ['Metric', 'Value'],
                ['Total Expenses', number_format($expenses, 2)],
                ['Total Income', number_format($income, 2)],
                ['Profit', number_format($profit, 2)],
                ['Egg Crates', $eggCrates],
                ['Feed (kg)', $feedKg],
                ['Total Birds', $totalBirds],
                ['Layers', Bird::where('type', 'layer')->whereNull('deleted_at')->sum('quantity') ?? 0],
                ['Broilers', Bird::where('type', 'broiler')->whereNull('deleted_at')->sum('quantity') ?? 0],
                ['Mortality Rate (%)', $mortalityRate],
                ['FCR', $fcr],
                ['Employees', Employee::whereNull('deleted_at')->count() ?? 0],
                ['Payroll', number_format(Payroll::whereBetween('pay_date', [$start, $end])->whereNull('deleted_at')->sum('net_pay') ?? 0, 2)],
                ['Sales', number_format(Sale::whereBetween('sale_date', [$start, $end])->whereNull('deleted_at')->sum('total_amount') ?? 0, 2)],
                ['Customers', Customer::whereNull('deleted_at')->count() ?? 0],
            ];

            $filename = "dashboard_export_" . now()->format('Ymd_His') . ".csv";
            $handle = fopen('php://output', 'w');
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            fputs($handle, "\xEF\xBB\xBF");
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
            exit;

        } catch (\Exception $e) {
            Log::error('Export error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export data.');
        }
    }
}