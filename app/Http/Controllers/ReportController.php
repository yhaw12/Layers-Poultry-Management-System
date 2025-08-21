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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getReportData(Request $request, $reportType)
    {
        if (!Auth::check()) {
            throw new \Exception('User must be authenticated.');
        }

        $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());
        $cacheKey = "report_{$reportType}_{$start}_{$end}_" . Auth::id();
        $data = Cache::remember($cacheKey, 300, function () use ($request, $reportType, $start, $end) {
            $data = [];

            if ($reportType === 'weekly') {
                $data['weekly'] = Egg::select(
                    DB::raw('YEAR(date_laid) as year'),
                    DB::raw('WEEK(date_laid, 1) as week'),
                    DB::raw('SUM(crates) as total') // Changed from sold_quantity to crates
                )
                    ->where('date_laid', '>=', now()->subWeeks(8))
                    ->whereNull('deleted_at')
                    ->groupBy('year', 'week')
                    ->orderBy('year', 'desc')
                    ->orderBy('week', 'desc')
                    ->get();
            } elseif ($reportType === 'monthly') {
                $data['monthly'] = Egg::select(
                    DB::raw('YEAR(date_laid) as year'),
                    DB::raw('MONTH(date_laid) as month_num'),
                    DB::raw('SUM(crates) as total')
                )
                    ->where('date_laid', '>=', now()->subMonths(6))
                    ->whereNull('deleted_at')
                    ->groupBy('year', 'month_num')
                    ->orderBy('year', 'desc')
                    ->orderBy('month_num', 'desc')
                    ->get();
            } elseif ($reportType === 'custom') {
                $this->validate($request, [
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                    'metrics' => 'required|array',
                    'metrics.*' => 'in:eggs,sales,expenses,payrolls,transactions',
                ]);

                if (in_array('eggs', $request->input('metrics', []))) {
                    $data['eggs'] = Egg::whereBetween('date_laid', [$start, $end])
                        ->whereNull('deleted_at')
                        ->select('date_laid', 'crates') // Changed quantity to crates
                        ->orderBy('date_laid')
                        ->get();
                }

                if (in_array('sales', $request->input('metrics', []))) {
                    $data['sales'] = Sale::with('customer', 'saleable')
                        ->whereBetween('sale_date', [$start, $end])
                        ->whereNull('deleted_at')
                        ->select('sale_date', 'customer_id', 'saleable_id', 'saleable_type', 'quantity', 'total_amount')
                        ->orderBy('sale_date')
                        ->get();
                }

                if (in_array('expenses', $request->input('metrics', []))) {
                    $data['expenses'] = Expense::whereBetween('date', [$start, $end])
                        ->whereNull('deleted_at')
                        ->select('date', 'description', 'amount')
                        ->orderBy('date')
                        ->get();
                }

                if (in_array('payrolls', $request->input('metrics', []))) {
                    $data['payrolls'] = Payroll::with('employee')
                        ->whereBetween('pay_date', [$start, $end])
                        ->whereNull('deleted_at')
                        ->select('pay_date', 'employee_id', 'base_salary', 'bonus', 'deductions', 'net_pay', 'status')
                        ->orderBy('pay_date')
                        ->get();
                }

                if (in_array('transactions', $request->input('metrics', []))) {
                    $data['transactions'] = Transaction::with('source')
                        ->whereBetween('date', [$start, $end])
                        ->whereNull('deleted_at')
                        ->select('date', 'type', 'amount', 'status', 'description', 'reference_id', 'reference_type')
                        ->orderBy('date')
                        ->get();
                }
            } elseif ($reportType === 'profitability') {
                $totalExpenses = Expense::whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0;
                $totalPayroll = Payroll::whereBetween('pay_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('net_pay') ?? 0;
                $totalOperationalCost = $totalExpenses + $totalPayroll;

                $data['profitability'] = Bird::select(
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

                $birdCount = $data['profitability']->count();
                if ($birdCount > 0) {
                    $expensePerBird = $totalOperationalCost / $birdCount;
                    foreach ($data['profitability'] as $row) {
                        $row->operational_cost = $expensePerBird;
                        // Profit already includes per-bird expense in query
                    }
                } else {
                    $data['profitability']->push((object)[
                        'bird_id' => null,
                        'breed' => 'N/A',
                        'type' => 'N/A',
                        'sales' => 0,
                        'feed_cost' => 0,
                        'total_expenses' => $totalExpenses,
                        'total_payroll' => $totalPayroll,
                        'operational_cost' => $totalOperationalCost,
                        'profit' => -$totalOperationalCost,
                    ]);
                }
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

            return $data;
        });

        return $data;
    }

    public function index(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please log in to access reports.');
            }

            $reportType = $request->query('type', 'weekly');
            $data = $this->getReportData($request, $reportType);
            return view('reports.index', compact('reportType', 'data'));
        } catch (\Exception $e) {
            Log::error('Failed to load report', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load report.');
        }
    }

    public function export(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $type = $request->query('type', 'weekly');
            $format = $request->query('format', 'pdf');
            $data = $this->getReportData($request, $type);

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('reports.index_pdf', compact('type', 'data'));
                return $pdf->download("report_{$type}_" . now()->format('Ymd') . '.pdf');
            } elseif ($format === 'excel') {
                return Excel::download(new CustomReportExport($data), "report_{$type}_" . now()->format('Ymd') . '.xlsx');
            }

            return redirect()->back()->with('error', 'Invalid export format.');
        } catch (\Exception $e) {
            Log::error('Failed to export report', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to export report.');
        }
    }

    public function custom(Request $request)
    {
        return $this->index($request);
    }
}
