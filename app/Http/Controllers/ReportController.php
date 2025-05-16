<?php

namespace App\Http\Controllers;

use App\Exports\CustomReportExport;
use App\Models\Egg;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Bird;
use App\Models\Feed;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Fetch report data based on the request parameters.
     */
    private function getReportData(Request $request, $reportType)
{
    $data = [];

    if ($reportType === 'weekly') {
        $data['weekly'] = Egg::select(
            DB::raw('YEAR(date_laid) as year'),
            DB::raw('WEEK(date_laid, 1) as week'),
            DB::raw('SUM(sold_quantity) as total')
        )
            ->where('date_laid', '>=', now()->subWeeks(8))
            ->groupBy('year', 'week')
            ->orderBy('year', 'desc')
            ->orderBy('week', 'desc')
            ->get();
    } elseif ($reportType === 'monthly') {
        $data['monthly'] = Egg::select(
            DB::raw('YEAR(date_laid) as year'),
            DB::raw('MONTH(date_laid) as month_num'),
            DB::raw('SUM(sold_quantity) as total')
        )
            ->where('date_laid', '>=', now()->subMonths(6))
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

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $metrics = $request->input('metrics', []);

        if (in_array('eggs', $metrics)) {
            $data['eggs'] = Egg::whereBetween('date_laid', [$startDate, $endDate])
                ->select('date_laid', 'sold_quantity')
                ->orderBy('date_laid')
                ->get();
        }

        if (in_array('sales', $metrics)) {
            $data['sales'] = Sale::with('customer', 'saleable')
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->select('sale_date', 'customer_id', 'saleable_id', 'saleable_type', 'quantity', 'total_amount')
                ->orderBy('sale_date')
                ->get();
        }

        if (in_array('expenses', $metrics)) {
            $data['expenses'] = Expense::whereBetween('date', [$startDate, $endDate])
                ->select('date', 'description', 'amount')
                ->orderBy('date')
                ->get();
        }
    } elseif ($reportType === 'profitability') {
        $data['profitability'] = Bird::select(
            'birds.id as bird_id',
            'birds.breed',
            DB::raw('COALESCE(SUM(sales.total_amount), 0) as sales'),
            DB::raw('COALESCE(SUM(feeds.quantity * feeds.unit_price), 0) as feed_cost'),
            DB::raw('COALESCE(SUM(expenses.amount), 0) as expenses'),
            DB::raw('COALESCE(SUM(sales.total_amount), 0) - COALESCE(SUM(feeds.quantity * feeds.unit_price), 0) - COALESCE(SUM(expenses.amount), 0) as profit')
        )
            ->leftJoin('sales', function ($join) {
                $join->on('birds.id', '=', 'sales.saleable_id')
                    ->where('sales.saleable_type', '=', Bird::class);
            })
            ->leftJoin('feeds', 'birds.id', '=', 'feeds.bird_id')
            ->leftJoin('expenses', 'birds.id', '=', 'expenses.bird_id')
            ->groupBy('birds.id', 'birds.breed')
            ->get();
    }

    return $data;
}

    /**
     * Display the report index page.
     */
    public function index(Request $request)
{
    $reportType = $request->query('type', 'weekly');
    $data = $this->getReportData($request, $reportType);
    return view('reports.index', compact('reportType', 'data'));
}

    /**
     * Export the report as PDF or Excel.
     */
    public function export(Request $request)
    {
        $type = $request->query('type', 'weekly');
        $format = $request->query('format', 'pdf');

        // Call index logic directly without rendering the view
        $reportType = $type;
        $data = $this->getReportData($request, $reportType);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.index_pdf', compact('type', 'data'));
            return $pdf->download("report_{$type}_" . now()->format('Ymd') . '.pdf');
        } elseif ($format === 'excel') {
            return Excel::download(new CustomReportExport($data), "report_{$type}_" . now()->format('Ymd') . '.xlsx');
        }

        return redirect()->back()->with('error', 'Invalid export format.');
    }

    /**
     * Handle custom report requests.
     */
    public function custom(Request $request)
    {
        return $this->index($request);
    }
}