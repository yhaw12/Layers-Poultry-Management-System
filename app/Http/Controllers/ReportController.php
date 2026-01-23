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
use App\Models\Mortalities; // Ensure this model exists
use App\Models\Payment;
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

use GuzzleHttp\Client;

class ReportController extends Controller
{
    protected int $cacheTTL;

    public function __construct()
    {
        $this->middleware('auth');
        // Cache duration in seconds (default 5 minutes)
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
        $compareFlag = $request->input('compare', '0') === '1';
        $cacheKey = "report_{$reportType}_u{$userId}_s{$start->timestamp}_e{$end->timestamp}_c{$compareFlag}";

        // Clear cache if debugging (optional, remove in production)
        // Cache::forget($cacheKey);

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($request, $reportType, $start, $end, $compareFlag) {
            $data = [];
            $startStr = $start->toDateTimeString();
            $endStr = $end->toDateTimeString();
            $daysInPeriod = $start->diffInDays($end) + 1;

            // --- 1. CORE FINANCIALS (KPIs) ---
            $totalIncome = Income::whereBetween('date', [$startStr, $endStr])->withoutTrashed()->sum('amount') ?? 0;
            $totalExpenses = Expense::whereBetween('date', [$startStr, $endStr])->withoutTrashed()->sum('amount') ?? 0;
            $totalPayroll = Payroll::whereBetween('pay_date', [$startStr, $endStr])->whereNull('deleted_at')->sum('net_pay') ?? 0;
            $totalOperationalCost = $totalExpenses + $totalPayroll;

            $data['profit_loss'] = [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'total_payroll' => $totalPayroll,
                'profit_loss' => $totalIncome - $totalOperationalCost,
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ];

            // --- 2. GLOBAL CHART DATA (Populates Trends & Comparisons Tabs) ---
            $data['charts'] = [
                'eggTrend' => Egg::selectRaw("DATE_FORMAT(date_laid, '%Y-%m-%d') as date, SUM(crates) as value")
                    ->whereBetween('date_laid', [$startStr, $endStr])->withoutTrashed()->groupBy('date')->orderBy('date')->get(),
                
                'feedTrend' => Feed::selectRaw("DATE(purchase_date) as date, SUM(quantity) as value")
                    ->whereBetween('purchase_date', [$startStr, $endStr])->withoutTrashed()->groupBy('date')->orderBy('date')->get(),
                
                'salesTrend' => Sale::selectRaw("DATE(sale_date) as date, SUM(total_amount) as value")
                    ->whereBetween('sale_date', [$startStr, $endStr])->withoutTrashed()->groupBy('date')->orderBy('date')->get(),
                
                'incomeTrend' => Income::selectRaw("DATE(date) as date, SUM(amount) as value")
                    ->whereBetween('date', [$startStr, $endStr])->withoutTrashed()->groupBy('date')->orderBy('date')->get(),
                
                'salesComparison' => Sale::select(
                        DB::raw('DATE(sale_date) as date'),
                        DB::raw("SUM(CASE WHEN saleable_type LIKE '%Egg%' THEN total_amount ELSE 0 END) as egg_sales"),
                        DB::raw("SUM(CASE WHEN saleable_type LIKE '%Bird%' THEN total_amount ELSE 0 END) as bird_sales")
                    )
                    ->whereBetween('sale_date', [$startStr, $endStr])->withoutTrashed()->groupBy('date')->orderBy('date')->get(),

                'invoiceStatuses' => [
                    'Pending' => Sale::where('status', 'pending')->whereBetween('sale_date', [$startStr, $endStr])->count(),
                    'Paid' => Sale::where('status', 'paid')->whereBetween('sale_date', [$startStr, $endStr])->count(),
                    'Partial' => Sale::where('status', 'partially_paid')->whereBetween('sale_date', [$startStr, $endStr])->count(),
                    'Overdue' => Sale::where('status', 'overdue')->whereBetween('sale_date', [$startStr, $endStr])->count(),
                ],

                // NEW: Transaction Trend for Payments Tab
                'transactionTrend' => Transaction::selectRaw("DATE(date) as date, SUM(amount) as value")
                    ->whereBetween('date', [$startStr, $endStr])
                    ->whereNull('deleted_at')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
            ];

            // --- 3. ADVANCED METRICS ---
            $eggTotal = $data['charts']['eggTrend']->sum('value');
            $data['avg_crates_per_day'] = $daysInPeriod > 0 ? $eggTotal / $daysInPeriod : 0;
            
            $totalFeedKg = $data['charts']['feedTrend']->sum('value');
            $data['advanced_metrics'] = [
                'economic_fcr' => ($totalIncome > 0) ? round(($totalFeedKg * 150 / $totalIncome) * 100, 2) : 0, 
                'stock_aging_days' => 0, // Placeholder
                'spoilage_risk' => 'Low'
            ];

            // --- 4. TAB-SPECIFIC DATA ---
            if ($reportType === 'weekly') {
                $data['weekly'] = Egg::selectRaw('YEAR(date_laid) as year, WEEK(date_laid, 1) as week, SUM(crates) as total')
                    ->whereBetween('date_laid', [$startStr, $endStr])->withoutTrashed()->groupBy('year', 'week')->orderBy('year', 'desc')->orderBy('week', 'desc')->get();
            } 
            elseif ($reportType === 'monthly') {
                $data['monthly'] = Egg::selectRaw('YEAR(date_laid) as year, MONTH(date_laid) as month_num, SUM(crates) as total')
                    ->whereBetween('date_laid', [$startStr, $endStr])->withoutTrashed()->groupBy('year', 'month_num')->orderBy('year', 'desc')->orderBy('month_num', 'desc')->get();
            } 
            elseif ($reportType === 'efficiency') {
                $data['efficiency'] = [
                    'mortality_trend' => Mortalities::selectRaw("DATE(date) as date, SUM(quantity) as value")
                        ->whereBetween('date', [$startStr, $endStr])->groupBy('date')->orderBy('date')->get(),
                    'expense_breakdown' => Expense::selectRaw("category, SUM(amount) as total")
                        ->whereBetween('date', [$startStr, $endStr])->groupBy('category')->get(),
                    'egg_grades' => Egg::selectRaw("egg_size, SUM(crates) as total")
                        ->whereBetween('date_laid', [$startStr, $endStr])->whereNotNull('egg_size')->groupBy('egg_size')->get(),
                    'top_customers' => Sale::select('customer_id', DB::raw('SUM(total_amount) as total_spent'))
                        ->whereBetween('sale_date', [$startStr, $endStr])->with('customer:id,name')->groupBy('customer_id')->orderByDesc('total_spent')->limit(5)->get()
                        ->map(fn($s) => ['name' => $s->customer->name ?? 'Unknown', 'total' => $s->total_spent]),
                ];
            } 
            elseif ($reportType === 'payments') {
                $data['payments'] = [
                    'chartData' => Payment::selectRaw('SUM(amount) as total')->whereBetween('payment_date', [$startStr, $endStr])->groupBy('payment_method')->pluck('total'),
                    'chartLabels' => Payment::selectRaw('payment_method')->whereBetween('payment_date', [$startStr, $endStr])->groupBy('payment_method')->pluck('payment_method')->map(fn($m)=>ucfirst(str_replace('_',' ',$m))),
                    'list' => Payment::with('customer')->whereBetween('payment_date', [$startStr, $endStr])->latest()->limit(10)->get(),
                    'detailed' => Payment::with('customer')->whereBetween('payment_date', [$startStr, $endStr])->orderBy('payment_date', 'desc')->get()
                ];
            }
            elseif ($reportType === 'forecast') {
                $dailyInc = $daysInPeriod > 0 ? $totalIncome / $daysInPeriod : 0;
                $dailyExp = $daysInPeriod > 0 ? $totalExpenses / $daysInPeriod : 0;
                $data['forecast'] = [
                    'forecasted_income' => $dailyInc * 30 * 1.05,
                    'forecasted_expenses' => $dailyExp * 30 * 1.02,
                    'forecasted_profit' => ($dailyInc * 30 * 1.05) - ($dailyExp * 30 * 1.02)
                ];
            }
            elseif ($reportType === 'custom') {
                $metrics = $request->input('metrics', []);
                if (in_array('sales', $metrics)) $data['sales'] = Sale::with('customer')->whereBetween('sale_date', [$startStr, $endStr])->withoutTrashed()->orderBy('sale_date')->get();
                // Add other custom logic here if needed
            }

            // --- 5. Return Merge with Totals ---
            return array_merge([
                'totalTransactions' => Transaction::whereBetween('date', [$startStr, $endStr])->whereNull('deleted_at')->sum('amount'),
                'totals' => ['income' => $totalIncome, 'profit' => $totalIncome - $totalOperationalCost]
            ], $data);
        });
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

            $reportType = $request->query('type', 'trends');
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

    public function data(Request $request)
    {
        try {
            $type = $request->query('type', 'trends');
            $payload = $this->getReportData($request, $type);
            return response()->json(['success' => true, 'reportType' => $type, 'data' => $payload]);
        } catch (\Exception $e) {
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
     */
    protected function generateChartImages(string $type, array $data): array
    {
        $images = [];

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

        try {
            // Standard Charts
            if (!empty($data['eggProduction']) && $data['eggProduction'] instanceof Collection) {
                $labels = $data['eggProduction']->pluck('date')->map(fn($d)=> (string)$d)->values()->all();
                $values = $data['eggProduction']->pluck('value')->map(fn($v)=> (float)$v)->values()->all();
                $conf = $makeConfig($labels, $values, 'Egg Production');
                $img = $this->quickChartFetch($conf);
                if ($img) $images['eggProduction'] = ['title' => 'Egg Production', 'image' => $img];
            }

            if (!empty($data['incomeData']) && $data['incomeData'] instanceof Collection) {
                $labels = $data['incomeData']->pluck('date')->map(fn($d)=> (string)$d)->values()->all();
                $values = $data['incomeData']->pluck('value')->map(fn($v)=> (float)$v)->values()->all();
                $conf = $makeConfig($labels, $values, 'Income');
                $img = $this->quickChartFetch($conf);
                if ($img) $images['income'] = ['title' => 'Income', 'image' => $img];
            }

            if (!empty($data['expenseData']) && $data['expenseData'] instanceof Collection) {
                $labels = $data['expenseData']->pluck('date')->map(fn($d)=> (string)$d)->values()->all();
                $values = $data['expenseData']->pluck('value')->map(fn($v)=> (float)$v)->values()->all();
                $conf = $makeConfig($labels, $values, 'Expenses');
                $img = $this->quickChartFetch($conf);
                if ($img) $images['expenses'] = ['title' => 'Expenses', 'image' => $img];
            }

            // Efficiency Charts (Mortality, Breakdown, Egg Grades)
            if (!empty($data['efficiency']['mortality_trend']) && $data['efficiency']['mortality_trend'] instanceof Collection) {
                $labels = $data['efficiency']['mortality_trend']->pluck('date')->map(fn($d) => (string)$d)->all();
                $values = $data['efficiency']['mortality_trend']->pluck('value')->all();
                
                $conf = $makeConfig($labels, $values, 'Mortality Trend');
                $conf['data']['datasets'][0]['borderColor'] = 'red';
                $conf['data']['datasets'][0]['backgroundColor'] = 'rgba(255, 99, 132, 0.2)';
                
                $img = $this->quickChartFetch($conf);
                if ($img) $images['mortality'] = ['title' => 'Mortality Trend', 'image' => $img];
            }

            if (!empty($data['efficiency']['expense_breakdown']) && $data['efficiency']['expense_breakdown'] instanceof Collection) {
                $labels = $data['efficiency']['expense_breakdown']->pluck('category')->all();
                $values = $data['efficiency']['expense_breakdown']->pluck('total')->all();

                $pieConfig = [
                    'type' => 'pie',
                    'data' => [
                        'labels' => $labels,
                        'datasets' => [[
                            'data' => $values,
                            'backgroundColor' => ['#4CAF50', '#FFC107', '#F44336', '#2196F3', '#9C27B0']
                        ]]
                    ],
                    'options' => [
                        'plugins' => [
                            'title' => ['display' => true, 'text' => 'Expense Breakdown']
                        ]
                    ]
                ];

                $img = $this->quickChartFetch($pieConfig);
                if ($img) $images['expense_breakdown'] = ['title' => 'Expenses by Category', 'image' => $img];
            }

            if (!empty($data['efficiency']['egg_grades']) && $data['efficiency']['egg_grades'] instanceof Collection) {
                $labels = $data['efficiency']['egg_grades']->pluck('egg_size')->map(fn($s) => ucfirst($s ?? 'Unsorted'))->all();
                $values = $data['efficiency']['egg_grades']->pluck('total')->all();

                if (count($values) > 0) {
                    $pieConfig = [
                        'type' => 'doughnut',
                        'data' => [
                            'labels' => $labels,
                            'datasets' => [[
                                'data' => $values,
                                'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'] 
                            ]]
                        ],
                        'options' => [
                            'plugins' => [
                                'title' => ['display' => true, 'text' => 'Production by Egg Size'],
                                'legend' => ['position' => 'right']
                            ]
                        ]
                    ];

                    $img = $this->quickChartFetch($pieConfig);
                    if ($img) $images['egg_grades'] = ['title' => 'Egg Grades', 'image' => $img];
                }
            }

        } catch (\Exception $e) {
            Log::warning('generateChartImages failed partially', ['err' => $e->getMessage()]);
        }

        return $images;
    }

    /**
     * Helper that given a Chart.js config array will attempt to fetch a PNG image from QuickChart
     */
    protected function quickChartFetch(array $chartConfig, int $width = 800, int $height = 400): ?string
    {
        try {
            $chartJson = json_encode($chartConfig, JSON_UNESCAPED_SLASHES);
            $encoded = rawurlencode($chartJson);
            $url = "https://quickchart.io/chart?width={$width}&height={$height}&format=png&chart={$encoded}";

            if (class_exists(Client::class)) {
                $client = new Client(['timeout' => 10.0]);
                $resp = $client->get($url);
                if ($resp->getStatusCode() === 200) {
                    $body = $resp->getBody()->getContents();
                    return 'data:image/png;base64,' . base64_encode($body);
                }
                return null;
            }

            if (ini_get('allow_url_fopen')) {
                $body = @file_get_contents($url);
                if ($body !== false) {
                    return 'data:image/png;base64,' . base64_encode($body);
                }
            }

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
                    case 'efficiency':
                        if (isset($data['efficiency']['expense_breakdown'])) {
                            foreach ($data['efficiency']['expense_breakdown'] as $ex) {
                                $rows[] = ['Category' => $ex->category, 'Total' => $ex->total];
                            }
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
                        if (isset($rows[0])) {
                            fputcsv($fp, array_keys((array)$rows[0]));
                        }
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