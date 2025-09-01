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
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    protected int $cacheTTL;

    public function __construct()
    {
        $this->middleware('auth');

        // Cache TTL in seconds (5 minutes default) — change if needed
        $this->cacheTTL = config('reports.cache_ttl', 300);
    }

    /**
     * Normalize and validate date inputs. Returns [$start, $end].
     */
    protected function normalizeDates(Request $request): array
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        // Defaults: last 6 months range (as in original controller)
        $defaultStart = now()->subMonths(6)->startOfMonth()->toDateString();
        $defaultEnd = now()->endOfMonth()->toDateString();

        $start = $start ?: $defaultStart;
        $end = $end ?: $defaultEnd;

        // Ensure correct order and valid dates
        try {
            $s = \Carbon\Carbon::parse($start)->startOfDay()->toDateString();
            $e = \Carbon\Carbon::parse($end)->endOfDay()->toDateString();
        } catch (\Exception $eParse) {
            // fallback defaults if parse fails
            Log::warning('ReportController: invalid date parse, falling back to defaults', ['start' => $start, 'end' => $end, 'error' => $eParse->getMessage()]);
            $s = $defaultStart;
            $e = $defaultEnd;
        }

        return [$s, $e];
    }

    /**
     * Common trend queries reused by dashboard and reports to keep consistency.
     * Returns associative array of collections.
     */
    protected function getCommonTrends(string $start, string $end): array
    {
        $eggProduction = Egg::select(DB::raw('DATE(date_laid) as date'), DB::raw('SUM(crates) as value'))
            ->whereBetween('date_laid', [$start, $end])
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('DATE(date_laid)'))
            ->orderBy('date', 'asc')
            ->limit(500)
            ->get();

        $feedConsumption = Feed::select(DB::raw('DATE(purchase_date) as date'), DB::raw('SUM(quantity) as value'))
            ->whereBetween('purchase_date', [$start, $end])
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('DATE(purchase_date)'))
            ->orderBy('date', 'asc')
            ->limit(500)
            ->get();

        $incomeData = Income::select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as value'))
            ->whereBetween('date', [$start, $end])
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date', 'asc')
            ->limit(500)
            ->get();

        $expenseData = Expense::select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as value'))
            ->whereBetween('date', [$start, $end])
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date', 'asc')
            ->limit(500)
            ->get();

        $salesData = Sale::select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as value'))
            ->whereBetween('sale_date', [$start, $end])
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy('date', 'asc')
            ->limit(500)
            ->get();

        // Sales comparison: egg sales vs bird sales by date
        $salesComparison = Sale::select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw("SUM(CASE WHEN saleable_type = '".addslashes(Egg::class)."' THEN total_amount ELSE 0 END) as egg_sales"),
                DB::raw("SUM(CASE WHEN saleable_type = '".addslashes(Bird::class)."' THEN total_amount ELSE 0 END) as bird_sales")
            )
            ->whereBetween('sale_date', [$start, $end])
            ->whereNull('deleted_at')
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

    /**
     * Primary report data builder (cached).
     */
    private function getReportData(Request $request, string $reportType): array
    {
        if (!Auth::check()) {
            throw new \Exception('User must be authenticated.');
        }

        [$start, $end] = $this->normalizeDates($request);
        $userId = Auth::id();
        $cacheKey = "report_{$reportType}_{$start}_{$end}_user_{$userId}";

        $data = Cache::remember($cacheKey, $this->cacheTTL, function () use ($request, $reportType, $start, $end) {
            $data = [];

            // fetch common trends to reuse in multiple report types
            $trends = $this->getCommonTrends($start, $end);

            if ($reportType === 'weekly') {
                $weekly = Egg::select(
                    DB::raw('YEAR(date_laid) as year'),
                    DB::raw('WEEK(date_laid, 1) as week'),
                    DB::raw('SUM(crates) as total')
                )
                ->whereBetween('date_laid', [$start, $end])
                ->whereNull('deleted_at')
                ->groupBy('year', 'week')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc')
                ->get();

                $data['weekly'] = $weekly;
                // include trends for charting
                $data['eggProduction'] = $trends['eggProduction'];
            } elseif ($reportType === 'monthly') {
                $monthly = Egg::select(
                    DB::raw('YEAR(date_laid) as year'),
                    DB::raw('MONTH(date_laid) as month_num'),
                    DB::raw('SUM(crates) as total')
                )
                ->whereBetween('date_laid', [$start, $end])
                ->whereNull('deleted_at')
                ->groupBy('year', 'month_num')
                ->orderBy('year', 'desc')
                ->orderBy('month_num', 'desc')
                ->get();

                $data['monthly'] = $monthly;
                $data['eggProduction'] = $trends['eggProduction'];
            } elseif ($reportType === 'custom') {
                // Validate inputs for custom report
                try {
                    $request->validate([
                        'start_date' => 'required|date',
                        'end_date' => 'required|date|after_or_equal:start_date',
                        'metrics' => 'nullable|array',
                        'metrics.*' => 'in:eggs,sales,expenses,payrolls,transactions,feed,income',
                    ]);
                } catch (ValidationException $ve) {
                    // Convert validation exception into a safe structure for caching/returning
                    Log::warning('ReportController: custom validation failed', ['errors' => $ve->errors()]);
                    throw $ve;
                }

                $metrics = $request->input('metrics', []);

                if (in_array('eggs', $metrics, true)) {
                    $data['eggs'] = Egg::whereBetween('date_laid', [$start, $end])
                        ->whereNull('deleted_at')
                        ->select('date_laid', 'crates')
                        ->orderBy('date_laid')
                        ->get();
                } else {
                    $data['eggs'] = collect();
                }

                if (in_array('sales', $metrics, true)) {
                    $data['sales'] = Sale::with('customer', 'saleable')
                        ->whereBetween('sale_date', [$start, $end])
                        ->whereNull('deleted_at')
                        ->select('sale_date', 'customer_id', 'saleable_id', 'saleable_type', 'quantity', 'total_amount')
                        ->orderBy('sale_date')
                        ->get();
                } else {
                    $data['sales'] = collect();
                }

                if (in_array('expenses', $metrics, true)) {
                    $data['expenses'] = Expense::whereBetween('date', [$start, $end])
                        ->whereNull('deleted_at')
                        ->select('date', 'description', 'amount')
                        ->orderBy('date')
                        ->get();
                } else {
                    $data['expenses'] = collect();
                }

                if (in_array('payrolls', $metrics, true)) {
                    $data['payrolls'] = Payroll::with('employee')
                        ->whereBetween('pay_date', [$start, $end])
                        ->whereNull('deleted_at')
                        ->select('pay_date', 'employee_id', 'base_salary', 'bonus', 'deductions', 'net_pay', 'status')
                        ->orderBy('pay_date')
                        ->get();
                } else {
                    $data['payrolls'] = collect();
                }

                if (in_array('transactions', $metrics, true)) {
                    $data['transactions'] = Transaction::with('source')
                        ->whereBetween('date', [$start, $end])
                        ->whereNull('deleted_at')
                        ->select('date', 'type', 'amount', 'status', 'description', 'reference_id', 'reference_type')
                        ->orderBy('date')
                        ->get();
                } else {
                    $data['transactions'] = collect();
                }

                // include common trends for charts
                $data['eggProduction'] = $trends['eggProduction'];
                $data['incomeData'] = $trends['incomeData'];
                $data['expenseData'] = $trends['expenseData'];
                $data['salesComparison'] = $trends['salesComparison'];
            } elseif ($reportType === 'profitability') {
                $totalExpenses = Expense::whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0;

                $totalPayroll = Payroll::whereBetween('pay_date', [$start, $end])
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
                    $join->on('birds.id', '=', 'feed.bird_id')
                        ->whereBetween('feed.purchase_date', [$start, $end])
                        ->whereNull('feed.deleted_at');
                })
                ->whereNull('birds.deleted_at')
                ->groupBy('birds.id', 'birds.breed', 'birds.type')
                ->setBindings([$totalExpenses, $totalPayroll, $totalOperationalCost])
                ->get();

                // ensure operational_cost and profit keys exist for view consumption
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
                $totalIncome = Income::whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0;

                $totalExpenses = Expense::whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0;

                $totalPayroll = Payroll::whereBetween('pay_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('net_pay') ?? 0;

                $profitLoss = $totalIncome - ($totalExpenses + $totalPayroll);

                $data['profit_loss'] = [
                    'total_income' => $totalIncome,
                    'total_expenses' => $totalExpenses,
                    'total_payroll' => $totalPayroll,
                    'profit_loss' => $profitLoss,
                    'start' => $start,
                    'end' => $end,
                ];

                // include trend details
                $data['incomeData'] = $trends['incomeData'];
                $data['expenseData'] = $trends['expenseData'];
            } elseif ($reportType === 'forecast') {
                $pastIncome = Income::where('date', '>=', now()->subMonths(6))
                    ->whereNull('deleted_at')
                    ->sum('amount') / 6;

                $pastExpenses = Expense::where('date', '>=', now()->subMonths(6))
                    ->whereNull('deleted_at')
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

            // always include some safe default keys the views expect
            $defaults = [
                'weekly' => collect(),
                'monthly' => collect(),
                'eggs' => collect(),
                'sales' => collect(),
                'expenses' => collect(),
                'payrolls' => collect(),
                'transactions' => collect(),
                'profitability' => collect(),
                'profit_loss' => [],
                'forecast' => [],
                'eggProduction' => $trends['eggProduction'],
                'feedConsumption' => $trends['feedConsumption'],
                'incomeData' => $trends['incomeData'],
                'expenseData' => $trends['expenseData'],
                'salesData' => $trends['salesData'],
                'salesComparison' => $trends['salesComparison'],
            ];

            // merge defaults into data without overwriting explicit values
            $data = array_merge($defaults, $data);

            return $data;
        });

        return $data;
    }

    /**
     * GET /reports
     * Renders blade or returns JSON when requested via AJAX.
     */
    public function index(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please log in to access reports.');
            }

            $reportType = $request->query('type', 'weekly');
            $data = $this->getReportData($request, $reportType);

            // If AJAX or Accept JSON, return JSON payload (handy for SPA / frontend async)
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'reportType' => $reportType,
                    'data' => $data,
                ]);
            }

            return view('reports.index', compact('reportType', 'data'));
        } catch (ValidationException $ve) {
            // Return validation errors as JSON or redirect back with errors
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json(['errors' => $ve->errors()], 422);
            }
            return back()->withErrors($ve->errors());
        } catch (\Exception $e) {
            Log::error('Failed to load report', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json(['error' => 'Failed to load report.'], 500);
            }
            return back()->with('error', 'Failed to load report.');
        }
    }

    /**
     * A dedicated JSON endpoint for retrieving report data (useful for charting/AJAX)
     * GET /reports/data
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

    /**
     * Export report (pdf / excel) — unchanged behaviour but now uses normalized dates & safer caching.
     */
    public function export(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $type = $request->query('type', 'weekly');
            $format = $request->query('format', 'pdf');

            // normalize dates / validation is handled by getReportData
            $data = $this->getReportData($request, $type);

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('reports.index_pdf', compact('type', 'data'));
                return $pdf->download("report_{$type}_" . now()->format('Ymd') . '.pdf');
            }

            if ($format === 'excel') {
                return Excel::download(new CustomReportExport($data), "report_{$type}_" . now()->format('Ymd') . '.xlsx');
            }

            return redirect()->back()->with('error', 'Invalid export format.');
        } catch (\Exception $e) {
            Log::error('Failed to export report', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json(['error' => 'Failed to export report.'], 500);
            }
            return back()->with('error', 'Failed to export report.');
        }
    }

    /**
     * Alias for index to keep original route signature
     */
    public function custom(Request $request)
    {
        return $this->index($request);
    }
}
