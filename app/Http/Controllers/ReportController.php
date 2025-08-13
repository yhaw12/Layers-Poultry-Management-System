<?php

namespace App\Http\Controllers;

use App\Exports\CustomReportExport;
use App\Models\Egg;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Bird;
use App\Models\Feed;
use App\Models\Income;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getReportData(Request $request, $reportType)
    {
        $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());
        $cacheKey = "report_{$reportType}_{$start}_{$end}";
        $data = Cache::remember($cacheKey, 300, function () use ($request, $reportType, $start, $end) {
            $data = [];

            if ($reportType === 'weekly') {
                $data['weekly'] = Egg::select(
                    DB::raw('YEAR(date_laid) as year'),
                    DB::raw('WEEK(date_laid, 1) as week'),
                    DB::raw('SUM(sold_quantity) as total')
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
                    'metrics.*' => 'in:eggs,sales,expenses',
                ]);

                if (in_array('eggs', $request->input('metrics', []))) {
                    $data['eggs'] = Egg::whereBetween('date_laid', [$start, $end])
                        ->whereNull('deleted_at')
                        ->select('date_laid', 'quantity')
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
            } elseif ($reportType === 'profitability') {
                $totalExpenses = Expense::whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0;

                $data['profitability'] = Bird::select(
                    'birds.id as bird_id',
                    'birds.breed',
                    DB::raw('COALESCE(SUM(sales.total_amount), 0) as sales'),
                    DB::raw('COALESCE(SUM(feed.quantity * feed.cost), 0) as feed_cost'),
                    DB::raw('? as total_expenses'),
                    DB::raw('COALESCE(SUM(sales.total_amount), 0) - COALESCE(SUM(feed.quantity * feed.cost), 0) as profit')
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
                    ->groupBy('birds.id', 'birds.breed')
                    ->setBindings([$totalExpenses])
                    ->get();

                $birdCount = $data['profitability']->count();
                if ($birdCount > 0) {
                    $expensePerBird = $totalExpenses / $birdCount;
                    foreach ($data['profitability'] as $row) {
                        $row->expenses = $expensePerBird;
                        $row->profit -= $expensePerBird;
                    }
                } else {
                    $data['profitability']->push((object)[
                        'bird_id' => null,
                        'breed' => 'N/A',
                        'sales' => 0,
                        'feed_cost' => 0,
                        'expenses' => $totalExpenses,
                        'profit' => -$totalExpenses,
                    ]);
                }
            } elseif ($reportType === 'profit-loss') {
                $totalIncome = Income::whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0;
                $totalExpenses = Expense::whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0;
                $profitLoss = $totalIncome - $totalExpenses;

                $data['profit_loss'] = [
                    'total_income' => $totalIncome,
                    'total_expenses' => $totalExpenses,
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
                $forecastedIncome = $pastIncome * 1.05;
                $forecastedExpenses = $pastExpenses * 1.03;

                $data['forecast'] = [
                    'forecasted_income' => $forecastedIncome,
                    'forecasted_expenses' => $forecastedExpenses,
                ];
            }

            return $data;
        });

        return $data;
    }

    public function index(Request $request)
    {
        try {
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