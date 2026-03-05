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
use App\Models\Mortalities;
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

    // --- System Constants ---
    const EGGS_PER_CRATE = 30;
    const TARGET_LAY_RATE = 0.90; // 90%
    const ECONOMIC_FCR_MULTIPLIER = 150;
    const PRICE_PER_CRATE = 30;

    public function __construct()
    {
        $this->middleware('auth');
        // Cache duration in seconds (default 5 minutes)
        $this->cacheTTL = config('reports.cache_ttl', 300);
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

            [$start, $end] = $this->normalizeDates($request);
            $reportType = $request->query('type', 'trends');
            $data = $this->getReportData($request, $reportType);

            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'reportType' => $reportType,
                    'data' => $data,
                ]);
            }

            return view('reports.index', compact('reportType', 'data', 'start', 'end'));
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

    public function custom(Request $request)
    {
        return $this->index($request);
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
     * Central method returning structured data for ALL tabs simultaneously.
     */
    private function getReportData(Request $request, string $reportType): array
    {
        if (!Auth::check()) {
            throw new \Exception('User must be authenticated.');
        }

        [$start, $end] = $this->normalizeDates($request);
        $userId = Auth::id();
        $cacheKey = "report_all_u{$userId}_s{$start->timestamp}_e{$end->timestamp}";

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($start, $end) {
            $startStr = $start->toDateTimeString();
            $endStr = $end->toDateTimeString();
            $daysInPeriod = max(1, (int) round($start->diffInDays($end) + 1));

            $financials = $this->buildFinancialData($startStr, $endStr);
            $charts = $this->buildChartData($startStr, $endStr);
            $periodic = $this->buildPeriodicData($startStr, $endStr);
            $efficiency = $this->buildEfficiencyData($startStr, $endStr, $charts, $daysInPeriod);
            $eggIntel = $this->buildEggIntelligence($start, $end, $daysInPeriod);
            $payments = $this->buildPaymentsData($startStr, $endStr);

            return array_merge(
                $financials,
                ['charts' => $charts],
                $periodic,
                ['efficiency' => $efficiency['data']],
                ['advanced_metrics' => $efficiency['advanced']],
                ['egg_intelligence' => $eggIntel],
                ['payments' => $payments],
                ['avg_crates_per_day' => $efficiency['advanced']['avg_crates_per_day']],
                
                // Keep backward compatibility for export functions
                [
                    'eggProduction' => $charts['eggTrend'],
                    'incomeData' => $charts['incomeTrend'],
                    'expenseData' => $this->buildExpenseDataForExport($startStr, $endStr)
                ]
            );
        });
    }

    // =========================================================================
    // MODULAR DATA BUILDERS
    // =========================================================================

    private function buildFinancialData(string $start, string $end): array
    {
        $totalIncome = Income::whereBetween('date', [$start, $end])->withoutTrashed()->sum('amount') ?? 0.0;
        $totalExpenses = Expense::whereBetween('date', [$start, $end])->withoutTrashed()->sum('amount') ?? 0.0;
        $totalPayroll = Payroll::whereBetween('pay_date', [$start, $end])->whereNull('deleted_at')->sum('net_pay') ?? 0.0;
        $totalTransactions = Transaction::whereBetween('date', [$start, $end])->whereNull('deleted_at')->sum('amount') ?? 0.0;

        $totalOperationalCost = $totalExpenses + $totalPayroll;
        $netProfit = $totalIncome - $totalOperationalCost;

        return [
            'profit_loss' => [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'total_payroll' => $totalPayroll,
                'profit_loss' => $netProfit,
                'start' => explode(' ', $start)[0],
                'end' => explode(' ', $end)[0],
            ],
            'totals' => ['income' => $totalIncome, 'profit' => $netProfit],
            'totalTransactions' => $totalTransactions
        ];
    }

    private function buildChartData(string $start, string $end): array
    {
        return [
            'eggTrend' => Egg::selectRaw("DATE_FORMAT(date_laid, '%Y-%m-%d') as date, SUM(crates) as value")
                ->whereBetween('date_laid', [$start, $end])->withoutTrashed()->groupBy('date')->orderBy('date')->get(),
            'feedTrend' => Feed::selectRaw("DATE(purchase_date) as date, SUM(quantity) as value")
                ->whereBetween('purchase_date', [$start, $end])->withoutTrashed()->groupBy('date')->orderBy('date')->get(),
            'salesTrend' => Sale::selectRaw("DATE(sale_date) as date, SUM(total_amount) as value")
                ->whereBetween('sale_date', [$start, $end])->withoutTrashed()->groupBy('date')->orderBy('date')->get(),
            'incomeTrend' => Income::selectRaw("DATE(date) as date, SUM(amount) as value")
                ->whereBetween('date', [$start, $end])->withoutTrashed()->groupBy('date')->orderBy('date')->get(),
            'salesComparison' => Sale::selectRaw("
                    DATE(sale_date) as date, 
                    SUM(CASE WHEN saleable_type LIKE '%Egg%' THEN total_amount ELSE 0 END) as egg_sales,
                    SUM(CASE WHEN saleable_type LIKE '%Bird%' THEN total_amount ELSE 0 END) as bird_sales
                ")->whereBetween('sale_date', [$start, $end])->withoutTrashed()->groupBy('date')->orderBy('date')->get(),
            'invoiceStatuses' => [
                'Pending' => Sale::where('status', 'pending')->whereBetween('sale_date', [$start, $end])->count(),
                'Paid' => Sale::where('status', 'paid')->whereBetween('sale_date', [$start, $end])->count(),
                'Partial' => Sale::where('status', 'partially_paid')->whereBetween('sale_date', [$start, $end])->count(),
                'Overdue' => Sale::where('status', 'overdue')->whereBetween('sale_date', [$start, $end])->count(),
            ],
            'transactionTrend' => Transaction::selectRaw("DATE(date) as date, SUM(amount) as value")
                ->whereBetween('date', [$start, $end])->whereNull('deleted_at')->groupBy('date')->orderBy('date')->get(),
        ];
    }

    private function buildPeriodicData(string $start, string $end): array
    {
        return [
            'weekly' => Egg::selectRaw('YEAR(date_laid) as year, WEEK(date_laid, 1) as week, SUM(crates) as total')
                ->whereBetween('date_laid', [$start, $end])->withoutTrashed()->groupBy('year', 'week')->orderBy('year')->orderBy('week')->get(),
            'monthly' => Egg::selectRaw('YEAR(date_laid) as year, MONTH(date_laid) as month_num, SUM(crates) as total')
                ->whereBetween('date_laid', [$start, $end])->withoutTrashed()->groupBy('year', 'month_num')->orderBy('year')->orderBy('month_num')->get(),
        ];
    }

    private function buildEfficiencyData(string $start, string $end, array $charts, int $daysInPeriod): array
    {
        $totalFeedKg = $charts['feedTrend']->sum('value');
        $totalCrates = $charts['eggTrend']->sum('value');
        $totalBirds = max(1, Bird::count()); // Prevent division by zero
        $totalIncome = Income::whereBetween('date', [$start, $end])->withoutTrashed()->sum('amount') ?? 0;
        $totalPayroll = Payroll::whereBetween('pay_date', [$start, $end])->whereNull('deleted_at')->sum('net_pay') ?? 0;

        $avgCrates = $daysInPeriod > 0 ? $totalCrates / $daysInPeriod : 0;
        $actualLayRate = ($avgCrates * self::EGGS_PER_CRATE) / $totalBirds;
        $prodGap = self::TARGET_LAY_RATE > 0 ? (($this::TARGET_LAY_RATE - $actualLayRate) / $this::TARGET_LAY_RATE) * 100 : 0;

        // --- NEW DYNAMIC PRICE LOGIC ---
        // Calculate the average price per crate from actual sales in this period
        $averagePricePerCrate = Sale::where('saleable_type', 'App\Models\Egg') // Make sure this matches your DB exactly!
            ->whereBetween('sale_date', [$start, $end])
            ->withoutTrashed()
            ->selectRaw('SUM(total_amount) / NULLIF(SUM(quantity), 0) as avg_price')
            ->value('avg_price') ?? 30; // Fallback to 30 if no sales exist in this date range

        $unsoldCrates = 0; // Keep this at 0 until we build your unsold inventory tracking
        $deadMoney = $unsoldCrates * $averagePricePerCrate;
        // -------------------------------

        return [
            'data' => [
                'mortality_trend' => Mortalities::selectRaw("DATE(date) as date, SUM(quantity) as value")
                    ->whereBetween('date', [$start, $end])->groupBy('date')->orderBy('date')->get(),
                'expense_breakdown' => Expense::selectRaw("category, SUM(amount) as total")
                    ->whereBetween('date', [$start, $end])->groupBy('category')->get(),
                'egg_grades' => Egg::selectRaw("egg_size, SUM(crates) as total")
                    ->whereBetween('date_laid', [$start, $end])->whereNotNull('egg_size')->groupBy('egg_size')->get(),
                'fcr' => $totalCrates > 0 ? round($totalFeedKg / ($totalCrates * self::EGGS_PER_CRATE), 2) : 0,
                'total_feed' => $totalFeedKg,
                'total_crates' => $totalCrates,
                'medicine_cost' => Expense::where('category', 'medicine')->whereBetween('date', [$start, $end])->sum('amount'),
            ],
            'advanced' => [
                'avg_crates_per_day' => round($avgCrates, 1),
                'economic_fcr' => $totalIncome > 0 ? round(($totalFeedKg * self::ECONOMIC_FCR_MULTIPLIER / $totalIncome) * 100, 2) : 0,
                'production_gap' => round(max(0, $prodGap), 1),
                'dead_money' => $deadMoney, // <--- Now using the dynamic calculation
                'labor_efficiency' => $totalPayroll > 0 ? round($totalIncome / $totalPayroll, 2) : 0,
                'stock_aging_days' => 2, // Static for now
                'spoilage_risk' => 'Low'
            ]
        ];
    }

    private function buildEggIntelligence(Carbon $start, Carbon $end, int $daysInPeriod): array
    {
        $startStr = $start->toDateTimeString();
        $endStr = $end->toDateTimeString();

        $eggTrendData = Egg::selectRaw('DATE(date_laid) as date, SUM(crates) as crates, SUM(total_eggs) as total_eggs')
            ->whereBetween('date_laid', [$startStr, $endStr])
            ->withoutTrashed()
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalCrates = $eggTrendData->sum('crates');
        $activeDays = $eggTrendData->count();
        $avgDailyCrates = $activeDays > 0 ? round($totalCrates / $activeDays, 1) : 0;

        [$prevStart, $prevEnd] = $this->computePreviousPeriod($start, $end);
        $prevTotalCrates = Egg::whereBetween('date_laid', [$prevStart->toDateTimeString(), $prevEnd->toDateTimeString()])
            ->withoutTrashed()
            ->sum('crates');

        // Safe growth rate calculation
        if ($prevTotalCrates == 0 && $totalCrates > 0) {
            $growthRate = 100; // Going from 0 to something is effectively +100%
        } elseif ($prevTotalCrates == 0 && $totalCrates == 0) {
            $growthRate = 0;
        } else {
            $growthRate = round((($totalCrates - $prevTotalCrates) / $prevTotalCrates) * 100, 1);
        }

        $stdDev = $this->calculateStdDev($eggTrendData->pluck('crates')->toArray());
        $consistency = $avgDailyCrates > 0 ? round(100 - ($stdDev / $avgDailyCrates * 100), 1) : 0;

        return [
            'total_crates'         => (int) $totalCrates,
            'total_eggs'           => (int) $eggTrendData->sum('total_eggs'),
            'avg_daily_crates'     => $avgDailyCrates,
            'peak_day'             => $eggTrendData->sortByDesc('crates')->first()?->date ?? '—',
            'peak_crates'          => (int) ($eggTrendData->sortByDesc('crates')->first()?->crates ?? 0),
            'lowest_day'           => $eggTrendData->sortBy('crates')->first()?->date ?? '—',
            'lowest_crates'        => (int) ($eggTrendData->sortBy('crates')->first()?->crates ?? 0),
            'growth_rate'          => $growthRate,
            'consistency_score'    => max(0, min(100, $consistency)),
            'zero_production_days' => max(0, (int) round($daysInPeriod - $activeDays)),
            'insight'              => $this->generateEggInsight($growthRate, $consistency, $daysInPeriod - $activeDays),
        ];
    }

    private function buildPaymentsData(string $start, string $end): array
    {
        return [
            'chartData' => Payment::selectRaw('SUM(amount) as total, payment_method')->whereBetween('payment_date', [$start, $end])->groupBy('payment_method')->pluck('total', 'payment_method'),
            'list' => Payment::with('customer')->whereBetween('payment_date', [$start, $end])->latest()->limit(10)->get(),
        ];
    }

    private function buildExpenseDataForExport(string $start, string $end)
    {
        return Expense::select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as value'))
            ->whereBetween('date', [$start, $end])
            ->withoutTrashed()
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date', 'asc')
            ->limit(500)
            ->get();
    }

    // =========================================================================
    // UTILITIES & HELPERS
    // =========================================================================

    protected function normalizeDates(Request $request): array
    {
        try {
            $s = $request->filled('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : now()->subMonths(6)->startOfMonth();
            $e = $request->filled('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfMonth()->endOfDay();
        } catch (\Exception $e) {
            $s = now()->subMonths(6)->startOfMonth();
            $e = now()->endOfMonth()->endOfDay();
        }
        return [$s, $e];
    }

    protected function computePreviousPeriod(Carbon $start, Carbon $end): array
    {
        $diffDays = (int) round($start->diffInDays($end) + 1);
        $prevEnd = (clone $start)->subDay()->endOfDay();
        $prevStart = (clone $prevEnd)->subDays($diffDays - 1)->startOfDay();
        return [$prevStart, $prevEnd];
    }

    protected function calculateStdDev(array $numbers): float
    {
        if (count($numbers) < 2) return 0.0;
        $mean = array_sum($numbers) / count($numbers);
        $variance = array_sum(array_map(fn($x) => pow($x - $mean, 2), $numbers)) / count($numbers);
        return sqrt($variance);
    }

    protected function generateEggInsight(float $growthRate, float $consistency, int $zeroDays): string
    {
        $insights = [];

        // Trend Analysis
        if ($growthRate >= 10) {
            $insights[] = "<span class='font-bold text-emerald-600'>🚀 Surging Production:</span> Output is up +{$growthRate}% compared to the previous period.";
        } elseif ($growthRate <= -10) {
            $insights[] = "<span class='font-bold text-red-600'>⚠️ Production Drop:</span> Output fell by {$growthRate}%. Verify feed quality, water supply, and flock health immediately.";
        } else {
            $insights[] = "<span class='font-bold text-blue-600'>📈 Stable Output:</span> Production volumes are holding steady.";
        }

        // Consistency Analysis
        if ($consistency >= 85) {
            $insights[] = "Daily collection rates are highly consistent, indicating an unstressed flock and solid farm routines.";
        } elseif ($consistency <= 65) {
            $insights[] = "High day-to-day variance detected. Consider standardizing your feeding times and egg collection schedules.";
        }

        // Zero Days Warning
        if ($zeroDays > 0) {
            $insights[] = "<span class='text-amber-600'>Note: There were {$zeroDays} days with zero recorded production.</span> If this wasn't a record-keeping gap, investigate potential flock disturbances.";
        }

        if (empty($insights)) {
            return "Sufficient data is being gathered. Maintain current routines.";
        }

        return "<ul class='list-disc pl-5 space-y-2 mt-2 text-sm sm:text-base'><li>" . implode("</li><li>", $insights) . "</li></ul>";
    }

    // =========================================================================
    // EXPORT & PDF GENERATION LOGIC
    // =========================================================================

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
}