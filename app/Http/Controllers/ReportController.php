<?php

namespace App\Http\Controllers;

use App\Exports\CustomReportExport;
use App\Models\Bird;
use App\Models\Egg;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Feed;
use App\Models\Income;
use App\Models\Payroll;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;

use GuzzleHttp\Client; // optional; fallback provided if Guzzle not installed

class ReportController extends Controller
{
    protected int $cacheTTL;

    public function __construct()
    {
        $this->middleware('auth');
        // seconds; configurable in config/reports.php or default 3 seconds (dev-friendly)
        $this->cacheTTL = config('reports.cache_ttl', 300);
    }

    /**
     * Normalize dates and return Carbon objects (startOfDay, endOfDay)
     */
    protected function normalizeDates(Request $request): array
    {
        $startInput = $request->input('start_date');
        $endInput = $request->input('end_date');

        $defaultStart = now()->subMonths(6)->startOfMonth();
        $defaultEnd = now()->endOfMonth()->endOfDay();

        try {
            $s = $startInput ? Carbon::parse($startInput)->startOfDay() : $defaultStart;
            $e = $endInput ? Carbon::parse($endInput)->endOfDay() : $defaultEnd;
        } catch (\Exception $eParse) {
            Log::warning('ReportController: invalid date parse, falling back to defaults', ['start' => $startInput, 'end' => $endInput, 'error' => $eParse->getMessage()]);
            $s = $defaultStart;
            $e = $defaultEnd;
        }

        return [$s, $e];
    }

    protected function getCommonTrends(Carbon $start, Carbon $end): array
    {
        $startStr = $start->toDateTimeString();
        $endStr = $end->toDateTimeString();

        $eggProduction = Egg::select(DB::raw('DATE(date_laid) as date'), DB::raw('SUM(crates) as value'))
            ->whereBetween('date_laid', [$startStr, $endStr])
            ->withoutTrashed()
            ->groupBy(DB::raw('DATE(date_laid)'))
            ->orderBy('date', 'asc')
            ->limit(500)
            ->get();

        $feedConsumption = Feed::select(DB::raw('DATE(purchase_date) as date'), DB::raw('SUM(quantity) as value'))
            ->whereBetween('purchase_date', [$startStr, $endStr])
            ->withoutTrashed()
            ->groupBy(DB::raw('DATE(purchase_date)'))
            ->orderBy('date', 'asc')
            ->limit(500)
            ->get();

        $incomeData = Income::select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as value'))
            ->whereBetween('date', [$startStr, $endStr])
            ->withoutTrashed()
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date', 'asc')
            ->limit(500)
            ->get();

        $expenseData = Expense::select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as value'))
            ->whereBetween('date', [$startStr, $endStr])
            ->withoutTrashed()
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date', 'asc')
            ->limit(500)
            ->get();

        $salesData = Sale::select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as value'))
            ->whereBetween('sale_date', [$startStr, $endStr])
            ->withoutTrashed()
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy('date', 'asc')
            ->limit(500)
            ->get();

        $salesComparison = Sale::select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw("SUM(CASE WHEN saleable_type = '".addslashes(Egg::class)."' THEN total_amount ELSE 0 END) as egg_sales"),
                DB::raw("SUM(CASE WHEN saleable_type = '".addslashes(Bird::class)."' THEN total_amount ELSE 0 END) as bird_sales")
            )
            ->whereBetween('sale_date', [$startStr, $endStr])
            ->withoutTrashed()
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy('date', 'asc')
            ->limit(500)
            ->get();

        return [
            'eggProduction' => $eggProduction,
            'feedConsumption' => $feedConsumption,
            'incomeData' => $incomeData,
            'expenseData' => $expenseData,
            'salesData' => $salesData,
            'salesComparison' => $salesComparison,
        ];
    }

    protected function computePreviousPeriod(Carbon $start, Carbon $end): array
    {
        $diffDays = $start->diffInDays($end) + 1;
        $prevEnd = (clone $start)->subDay()->endOfDay();
        $prevStart = (clone $prevEnd)->subDays($diffDays - 1)->startOfDay();
        return [$prevStart, $prevEnd];
    }

    /**
     * Central method returning structured data for a given report type
     */
    private function getReportData(Request $request, string $reportType): array
    {
        if (!Auth::check()) {
            throw new \Exception('User must be authenticated.');
        }

        [$start, $end] = $this->normalizeDates($request);
        $userId = Auth::id();
        $compareFlag = $request->input('compare', '0') === '1' || $request->query('compare') === '1';
        $cacheKey = "report_{$reportType}_user_{$userId}_start_{$start->format('Ymd')}_end_{$end->format('Ymd')}_compare_" . ($compareFlag ? '1' : '0');

        $data = Cache::remember($cacheKey, $this->cacheTTL, function () use ($request, $reportType, $start, $end, $compareFlag) {
            $data = [];
            $trends = $this->getCommonTrends($start, $end);
            $startStr = $start->toDateTimeString();
            $endStr = $end->toDateTimeString();

            // Always compute common KPIs (profit_loss structure) for consistency across views
            $totalIncome = Income::whereBetween('date', [$startStr, $endStr])
                ->withoutTrashed()
                ->sum('amount') ?? 0;

            $totalExpenses = Expense::whereBetween('date', [$startStr, $endStr])
                ->withoutTrashed()
                ->sum('amount') ?? 0;

            $totalPayroll = Payroll::whereBetween('pay_date', [$startStr, $endStr])
                ->whereNull('deleted_at')
                ->sum('net_pay') ?? 0;

            $profitLoss = $totalIncome - ($totalExpenses + $totalPayroll);

            $data['profit_loss'] = [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'total_payroll' => $totalPayroll,
                'profit_loss' => $profitLoss,
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ];

            // Compute avg_crates_per_day for KPI
            $eggTotal = $trends['eggProduction']->sum('value');
            $days = $start->diffInDays($end) + 1;
            $data['avg_crates_per_day'] = $days > 0 ? $eggTotal / $days : 0;

            if ($reportType === 'weekly') {
                $weekly = Egg::select(
                    DB::raw('YEAR(date_laid) as year'),
                    DB::raw('WEEK(date_laid, 1) as week'),
                    DB::raw('SUM(crates) as total')
                )
                ->whereBetween('date_laid', [$start->toDateTimeString(), $end->toDateTimeString()])
                ->withoutTrashed()
                ->groupBy('year', 'week')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc')
                ->get();

                $data['weekly'] = $weekly;
                $data['eggProduction'] = $trends['eggProduction'];
            } elseif ($reportType === 'monthly') {
                $monthly = Egg::select(
                    DB::raw('YEAR(date_laid) as year'),
                    DB::raw('MONTH(date_laid) as month_num'),
                    DB::raw('SUM(crates) as total')
                )
                ->whereBetween('date_laid', [$start->toDateTimeString(), $end->toDateTimeString()])
                ->withoutTrashed()
                ->groupBy('year', 'month_num')
                ->orderBy('year', 'desc')
                ->orderBy('month_num', 'desc')
                ->get();

                $data['monthly'] = $monthly;
                $data['eggProduction'] = $trends['eggProduction'];
            } elseif ($reportType === 'custom') {
                try {
                    $request->validate([
                        'start_date' => 'required|date',
                        'end_date' => 'required|date|after_or_equal:start_date',
                        'metrics' => 'nullable|array',
                        'metrics.*' => 'in:eggs,sales,expenses,payrolls,transactions,feed,income',
                    ]);
                } catch (ValidationException $ve) {
                    Log::warning('ReportController: custom validation failed', ['errors' => $ve->errors()]);
                    throw $ve;
                }

                $metrics = $request->input('metrics', []);

                if (in_array('eggs', $metrics, true)) {
                    $data['eggs'] = Egg::whereBetween('date_laid', [$start->toDateTimeString(), $end->toDateTimeString()])
                        ->withoutTrashed()
                        ->select('date_laid', 'crates')
                        ->orderBy('date_laid')
                        ->get();
                } else {
                    $data['eggs'] = collect();
                }

                if (in_array('sales', $metrics, true)) {
                    $data['sales'] = Sale::with('customer', 'saleable')
                        ->whereBetween('sale_date', [$start->toDateTimeString(), $end->toDateTimeString()])
                        ->withoutTrashed()
                        ->select('sale_date', 'customer_id', 'saleable_id', 'saleable_type', 'quantity', 'total_amount')
                        ->orderBy('sale_date')
                        ->get();
                } else {
                    $data['sales'] = collect();
                }

                if (in_array('expenses', $metrics, true)) {
                    $data['expenses'] = Expense::whereBetween('date', [$start->toDateTimeString(), $end->toDateTimeString()])
                        ->withoutTrashed()
                        ->select('date', 'description', 'amount')
                        ->orderBy('date')
                        ->get();
                } else {
                    $data['expenses'] = collect();
                }

                if (in_array('payrolls', $metrics, true)) {
                    $data['payrolls'] = Payroll::with('employee')
                        ->whereBetween('pay_date', [$start->toDateTimeString(), $end->toDateTimeString()])
                        ->whereNull('deleted_at')
                        ->select('pay_date', 'employee_id', 'base_salary', 'bonus', 'deductions', 'net_pay', 'status')
                        ->orderBy('pay_date')
                        ->get();
                } else {
                    $data['payrolls'] = collect();
                }

                if (in_array('transactions', $metrics, true)) {
                    $data['transactions'] = Transaction::with('source')
                        ->whereBetween('date', [$start->toDateTimeString(), $end->toDateTimeString()])
                        ->withoutTrashed()
                        ->select('date', 'type', 'amount', 'status', 'description', 'reference_id', 'reference_type')
                        ->orderBy('date')
                        ->get();
                } else {
                    $data['transactions'] = collect();
                }

                $data['eggProduction'] = $trends['eggProduction'];
                $data['incomeData'] = $trends['incomeData'];
                $data['expenseData'] = $trends['expenseData'];
                $data['salesComparison'] = $trends['salesComparison'];
            } elseif ($reportType === 'profitability') {
                $totalExpenses = Expense::whereBetween('date', [$start->toDateTimeString(), $end->toDateTimeString()])
                    ->withoutTrashed()
                    ->sum('amount') ?? 0;

                $totalPayroll = Payroll::whereBetween('pay_date', [$start->toDateTimeString(), $end->toDateTimeString()])
                    ->whereNull('deleted_at')
                    ->sum('net_pay') ?? 0;

                $totalOperationalCost = $totalExpenses + $totalPayroll;

                $profitability = Bird::select(
                    'birds.id as bird_id',
                    'birds.breed',
                    'birds.type',
                    DB::raw('COALESCE(SUM(sales.total_amount), 0) as sales'),
                    DB::raw('COALESCE(SUM(feed.quantity * feed.cost), 0) as feed_cost'),
                    DB::raw('? as total_expenses'),
                    DB::raw('? as total_payroll'),
                    DB::raw('COALESCE(SUM(sales.total_amount), 0) - COALESCE(SUM(feed.quantity * feed.cost), 0) - (? / COUNT(DISTINCT birds.id)) as profit')
                )
                ->leftJoin('sales', function ($join) {
                    $join->on('birds.id', '=', 'sales.saleable_id')
                        ->where('sales.saleable_type', '=', Bird::class)
                        ->whereNull('sales.deleted_at');
                })
                ->leftJoin('feed', function ($join) use ($start, $end) {
                    // join to feed table (your migrations used table name 'feed')
                    $join->on('birds.id', '=', 'feed.bird_id')
                        ->whereBetween('feed.purchase_date', [$start->toDateTimeString(), $end->toDateTimeString()])
                        ->whereNull('feed.deleted_at');
                })
                ->whereNull('birds.deleted_at')
                ->groupBy('birds.id', 'birds.breed', 'birds.type')
                ->setBindings([$totalExpenses, $totalPayroll, $totalOperationalCost])
                ->get();

                $birdCount = $profitability->count();
                if ($birdCount > 0) {
                    $expensePerBird = $totalOperationalCost / $birdCount;
                    foreach ($profitability as $row) {
                        $row->operational_cost = $expensePerBird;
                        $row->profit = $row->profit ?? ($row->sales - ($row->feed_cost ?? 0) - $expensePerBird);
                    }
                }

                $data['profitability'] = $profitability;
                $data['incomeData'] = $trends['incomeData'];
                $data['expenseData'] = $trends['expenseData'];
            } elseif ($reportType === 'profit-loss') {
                // Already computed above
            } elseif ($reportType === 'forecast') {
                $pastIncome = Income::where('date', '>=', now()->subMonths(6))
                    ->withoutTrashed()
                    ->sum('amount') / 6;

                $pastExpenses = Expense::where('date', '>=', now()->subMonths(6))
                    ->withoutTrashed()
                    ->sum('amount') / 6;

                $pastPayroll = Payroll::where('pay_date', '>=', now()->subMonths(6))
                ->whereNull('deleted_at')
                ->sum('net_pay') / 6;

                $forecastedIncome = $pastIncome * 1.05;
                $forecastedExpenses = ($pastExpenses + $pastPayroll) * 1.03;

                $data['forecast'] = [
                    'forecasted_income' => $forecastedIncome,
                    'forecasted_expenses' => $forecastedExpenses,
                    'forecasted_profit' => $forecastedIncome - $forecastedExpenses,
                ];
            }

            // defaults to ensure keys exist
            $defaults = [
                'weekly' => collect(),
                'monthly' => collect(),
                'eggs' => collect(),
                'sales' => collect(),
                'expenses' => collect(),
                'payrolls' => collect(),
                'transactions' => collect(),
                'profitability' => collect(),
                'forecast' => [],
                'eggProduction' => $trends['eggProduction'],
                'feedConsumption' => $trends['feedConsumption'],
                'incomeData' => $trends['incomeData'],
                'expenseData' => $trends['expenseData'],
                'salesData' => $trends['salesData'],
                'salesComparison' => $trends['salesComparison'],
            ];

            $data = array_merge($defaults, $data);

            if ($compareFlag) {
                try {
                    [$prevStart, $prevEnd] = $this->computePreviousPeriod($start, $end);

                    $prevWeekly = Egg::select(
                        DB::raw('YEAR(date_laid) as year'),
                        DB::raw('WEEK(date_laid, 1) as week'),
                        DB::raw('SUM(crates) as total')
                    )
                    ->whereBetween('date_laid', [$prevStart->toDateTimeString(), $prevEnd->toDateTimeString()])
                    ->withoutTrashed()
                    ->groupBy('year', 'week')
                    ->orderBy('year', 'desc')
                    ->orderBy('week', 'desc')
                    ->get();

                    $prevMonthly = Egg::select(
                        DB::raw('YEAR(date_laid) as year'),
                        DB::raw('MONTH(date_laid) as month_num'),
                        DB::raw('SUM(crates) as total')
                    )
                    ->whereBetween('date_laid', [$prevStart->toDateTimeString(), $prevEnd->toDateTimeString()])
                    ->withoutTrashed()
                    ->groupBy('year', 'month_num')
                    ->orderBy('year', 'desc')
                    ->orderBy('month_num', 'desc')
                    ->get();

                    $data['prev_weekly'] = $prevWeekly;
                    $data['prev_monthly'] = $prevMonthly;
                    $data['prev_period'] = ['start' => $prevStart->toDateString(), 'end' => $prevEnd->toDateString()];
                } catch (\Exception $e) {
                    Log::warning('Failed to compute previous period data in getReportData', ['err' => $e->getMessage()]);
                    $data['prev_weekly'] = collect();
                    $data['prev_monthly'] = collect();
                }
            }

            return $data;
        });

        return $data;
    }

    /**
     * Index: server-rendered page
     */
    public function index(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please log in to access reports.');
            }

            $reportType = $request->query('type', 'weekly');
            $data = $this->getReportData($request, $reportType);

            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'reportType' => $reportType,
                    'data' => $data,
                ]);
            }

            return view('reports.index', compact('reportType', 'data'));
        } catch (ValidationException $ve) {
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $ve->errors()], 422);
            }
            return back()->withErrors($ve->errors());
        } catch (\Exception $e) {
            Log::error('Failed to load report', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Failed to load report.'], 500);
            }
            return back()->with('error', 'Failed to load report.');
        }
    }

    /**
     * data endpoint that returns JSON with consistent shape
     */
    public function data(Request $request)
    {
        try {
            $type = $request->query('type', 'weekly');
            $payload = $this->getReportData($request, $type);
            return response()->json(['success' => true, 'reportType' => $type, 'data' => $payload]);
        } catch (ValidationException $ve) {
            return response()->json(['success' => false, 'errors' => $ve->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Report data endpoint failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'error' => 'Failed to fetch report data.'], 500);
        }
    }

    public function prevData(Request $request)
    {
        try {
            [$start, $end] = $this->normalizeDates($request);
            [$prevStart, $prevEnd] = $this->computePreviousPeriod($start, $end);

            $prevWeekly = Egg::select(
                DB::raw('YEAR(date_laid) as year'),
                DB::raw('WEEK(date_laid, 1) as week'),
                DB::raw('SUM(crates) as total')
            )
            ->whereBetween('date_laid', [$prevStart->toDateTimeString(), $prevEnd->toDateTimeString()])
            ->withoutTrashed()
            ->groupBy('year', 'week')
            ->orderBy('year', 'desc')
            ->orderBy('week', 'desc')
            ->get();

            $prevMonthly = Egg::select(
                DB::raw('YEAR(date_laid) as year'),
                DB::raw('MONTH(date_laid) as month_num'),
                DB::raw('SUM(crates) as total')
            )
            ->whereBetween('date_laid', [$prevStart->toDateTimeString(), $prevEnd->toDateTimeString()])
            ->withoutTrashed()
            ->groupBy('year', 'month_num')
            ->orderBy('year', 'desc')
            ->orderBy('month_num', 'desc')
            ->get();

            return response()->json([
                'success' => true,
                'prev_weekly' => $prevWeekly,
                'prev_monthly' => $prevMonthly,
                'prev_start' => $prevStart->toDateString(),
                'prev_end' => $prevEnd->toDateString(),
            ]);
        } catch (\Exception $e) {
            Log::error('prevData failed', ['err' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Failed to compute previous period data.'], 500);
        }
    }

    /**
     * Generate chart images (base64 data URIs) for the PDF using QuickChart.io
     *
     * Note: QuickChart requires outbound HTTP access. This method attempts to use Guzzle if available,
     * and falls back to file_get_contents(). If neither works (e.g. no connectivity), it returns [].
     *
     * Returned array keys: e.g. ['eggProduction' => ['title' => 'Egg Production', 'image' => 'data:image/png;base64,...'], ...]
     */
    protected function generateChartImages(string $type, array $data): array
    {
        $images = [];

        // helper to build a simple Chart.js config
        $makeConfig = function (array $labels, array $values, string $title = '') {
            return [
                'type' => 'line',
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => $title ?: 'Series',
                            'data' => $values,
                            'fill' => true,
                            'tension' => 0.25
                        ]
                    ]
                ],
                'options' => [
                    'plugins' => [
                        'legend' => ['display' => false],
                        'title' => ['display' => (bool)$title, 'text' => $title]
                    ],
                    'scales' => [
                        'y' => ['beginAtZero' => true]
                    ]
                ]
            ];
        };

        // Map some common data sources to charts
        try {
            // eggProduction
            if (!empty($data['eggProduction']) && $data['eggProduction'] instanceof Collection) {
                $labels = $data['eggProduction']->pluck('date')->map(fn($d)=> (string)$d)->values()->all();
                $values = $data['eggProduction']->pluck('value')->map(fn($v)=> (float)$v)->values()->all();
                $conf = $makeConfig($labels, $values, 'Egg Production');
                $img = $this->quickChartFetch($conf);
                if ($img) $images['eggProduction'] = ['title' => 'Egg Production', 'image' => $img];
            }

            // incomeData
            if (!empty($data['incomeData']) && $data['incomeData'] instanceof Collection) {
                $labels = $data['incomeData']->pluck('date')->map(fn($d)=> (string)$d)->values()->all();
                $values = $data['incomeData']->pluck('value')->map(fn($v)=> (float)$v)->values()->all();
                $conf = $makeConfig($labels, $values, 'Income');
                $img = $this->quickChartFetch($conf);
                if ($img) $images['income'] = ['title' => 'Income', 'image' => $img];
            }

            // expenseData
            if (!empty($data['expenseData']) && $data['expenseData'] instanceof Collection) {
                $labels = $data['expenseData']->pluck('date')->map(fn($d)=> (string)$d)->values()->all();
                $values = $data['expenseData']->pluck('value')->map(fn($v)=> (float)$v)->values()->all();
                $conf = $makeConfig($labels, $values, 'Expenses');
                $img = $this->quickChartFetch($conf);
                if ($img) $images['expenses'] = ['title' => 'Expenses', 'image' => $img];
            }

            // salesComparison: has egg_sales and bird_sales per date
            if (!empty($data['salesComparison']) && $data['salesComparison'] instanceof Collection) {
                $labels = $data['salesComparison']->pluck('date')->map(fn($d)=> (string)$d)->values()->all();
                $eggSales = $data['salesComparison']->pluck('egg_sales')->map(fn($v)=> (float)$v)->values()->all();
                $birdSales = $data['salesComparison']->pluck('bird_sales')->map(fn($v)=> (float)$v)->values()->all();

                $conf = [
                    'type' => 'bar',
                    'data' => [
                        'labels' => $labels,
                        'datasets' => [
                            ['label' => 'Egg sales', 'data' => $eggSales],
                            ['label' => 'Bird sales', 'data' => $birdSales]
                        ]
                    ],
                    'options' => [
                        'plugins' => ['title' => ['display' => true, 'text' => 'Sales Comparison']],
                        'scales' => ['y' => ['beginAtZero' => true]]
                    ]
                ];
                $img = $this->quickChartFetch($conf);
                if ($img) $images['salesComparison'] = ['title' => 'Sales Comparison', 'image' => $img];
            }
        } catch (\Exception $e) {
            Log::warning('generateChartImages failed partially', ['err' => $e->getMessage()]);
        }

        return $images;
    }

    /**
     * Helper that given a Chart.js config array will attempt to fetch a PNG image from QuickChart
     * and return a data:image/png;base64,... string. Returns null on failure.
     *
     * This implementation first attempts to use Guzzle (if installed), otherwise falls back to file_get_contents.
     */
    protected function quickChartFetch(array $chartConfig, int $width = 800, int $height = 400): ?string
    {
        try {
            // encode config into query parameter
            $chartJson = json_encode($chartConfig, JSON_UNESCAPED_SLASHES);
            $encoded = rawurlencode($chartJson);
            $url = "https://quickchart.io/chart?width={$width}&height={$height}&format=png&chart={$encoded}";

            // Prefer Guzzle if available
            if (class_exists(Client::class)) {
                $client = new Client(['timeout' => 10.0]);
                $resp = $client->get($url);
                if ($resp->getStatusCode() === 200) {
                    $body = $resp->getBody()->getContents();
                    return 'data:image/png;base64,' . base64_encode($body);
                }
                return null;
            }

            // fallback - allow file_get_contents if allow_url_fopen enabled
            if (ini_get('allow_url_fopen')) {
                $body = @file_get_contents($url);
                if ($body !== false) {
                    return 'data:image/png;base64,' . base64_encode($body);
                }
            }

            // if both fail, log and return null
            Log::warning('quickChartFetch: HTTP fetch not possible (no Guzzle and allow_url_fopen disabled)');
            return null;
        } catch (\Throwable $e) {
            Log::warning('quickChartFetch failed', ['err' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Export handler (pdf/csv/excel)
     */
    public function export(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $type = $request->input('type', $request->query('type', 'weekly'));
            $format = $request->input('format', $request->query('format', 'pdf'));
            $columns = $request->input('columns', []);
            $includeChart = (bool) $request->input('include_chart', false);
            $includeSummary = (bool) $request->input('include_summary', false);
            $separateSheets = (bool) $request->input('separate_sheets', false);

            $data = $this->getReportData($request, $type);

            if ($includeChart) {
                try {
                    $chartImages = $this->generateChartImages($type, $data);
                    if (!empty($chartImages)) $data['chart_images'] = $chartImages;
                } catch (\Exception $e) {
                    Log::warning('Failed to create chart images for export', ['err' => $e->getMessage()]);
                }
            }

            if ($format === 'csv') {
                $rows = [];
                switch ($type) {
                    case 'weekly':
                        foreach ($data['weekly'] as $r) {
                            $rows[] = ['year' => $r->year, 'week' => $r->week, 'total' => $r->total];
                        }
                        break;
                    case 'monthly':
                        foreach ($data['monthly'] as $r) {
                            $rows[] = ['year' => $r->year, 'month' => $r->month_num, 'total' => $r->total];
                        }
                        break;
                    case 'custom':
                        $metrics = $request->input('metrics', []);
                        if (!empty($metrics)) {
                            $m = $metrics[0];
                            if (isset($data[$m]) && $data[$m] instanceof Collection) {
                                foreach ($data[$m] as $item) $rows[] = (array)$item;
                            }
                        }
                        break;
                }

                if (!empty($rows)) {
                    $fp = fopen('php://temp', 'w+');
                    if (!empty($columns)) {
                        fputcsv($fp, $columns);
                        foreach ($rows as $r) {
                            $row = array_map(fn($c) => $r[$c] ?? '', $columns);
                            fputcsv($fp, $row);
                        }
                    } else {
                        fputcsv($fp, array_keys((array)$rows[0]));
                        foreach ($rows as $r) fputcsv($fp, (array)$r);
                    }
                    rewind($fp);
                    $content = stream_get_contents($fp);
                    fclose($fp);

                    $filename = "report_{$type}_" . now()->format('Ymd') . '.csv';
                    return response($content, 200, [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename={$filename}",
                    ]);
                }
            }

            if ($format === 'pdf') {
                // render a PDF (blade reports.index_pdf should exist)
                $pdf = Pdf::loadView('reports.index_pdf', compact('type', 'data', 'columns', 'includeChart', 'includeSummary'));
                return $pdf->download("report_{$type}_" . now()->format('Ymd') . '.pdf');
            }

            if ($format === 'excel') {
                return Excel::download(new CustomReportExport($data, $columns, [
                    'includeChart' => $includeChart,
                    'includeSummary' => $includeSummary,
                    'separateSheets' => $separateSheets,
                ]), "report_{$type}_" . now()->format('Ymd') . '.xlsx');
            }

            return redirect()->back()->with('error', 'Invalid export format.');
        } catch (ValidationException $ve) {
            Log::warning('Export validation failed', ['errors' => $ve->errors()]);
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json(['errors' => $ve->errors()], 422);
            }
            return back()->withErrors($ve->errors());
        } catch (\Exception $e) {
            Log::error('Failed to export report', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json(['error' => 'Failed to export report.'], 500);
            }
            return back()->with('error', 'Failed to export report.');
        }
    }

    public function custom(Request $request)
    {
        return $this->index($request);
    }
}