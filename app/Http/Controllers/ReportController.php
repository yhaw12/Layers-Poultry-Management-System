<?php

namespace App\Http\Controllers;

use App\Models\Bird;
use App\Models\Egg;
use App\Models\Expense;
use App\Models\FeedConsumption;
use App\Models\Sale;
use App\Exports\CustomReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->input('type', 'weekly'); // Default to 'weekly' if no type is provided
        $data = [];

        // Weekly report
        if ($reportType === 'weekly') {
            $data['weekly'] = DB::table('eggs')
                ->selectRaw('WEEK(date_laid, 1) as week, YEAR(date_laid) as year, SUM(sold_quantity) as total')
                ->where('date_laid', '>=', now()->subWeeks(8))
                ->groupBy('week', 'year')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc')
                ->get();
        }

        // Monthly report
        if ($reportType === 'monthly') {
            $data['monthly'] = Egg::whereBetween('date_laid', [now()->subMonths(6), now()])
                ->selectRaw('YEAR(date_laid) as year, MONTH(date_laid) as month_num, SUM(crates) as total')
                ->groupBy('year', 'month_num')
                ->orderBy('year', 'desc')
                ->orderBy('month_num', 'desc')
                ->get();
        }

        // Profitability report
        if ($reportType === 'profitability') {
            $birds = Bird::all();
            $data['profitability'] = $birds->map(function ($bird) {
                $sales = Sale::where('saleable_type', Bird::class)
                    ->where('saleable_id', $bird->id)
                    ->sum('total_amount') ?? 0;
                $feedCost = FeedConsumption::where('bird_id', $bird->id)
                    ->sum('cost') ?? 0;
                $expenses = Expense::where('bird_id', $bird->id)
                    ->sum('amount') ?? 0;

                return (object) [
                    'bird_id' => $bird->id,
                    'breed' => $bird->breed,
                    'sales' => $sales,
                    'feed_cost' => $feedCost,
                    'expenses' => $expenses,
                    'profit' => $sales - ($feedCost + $expenses),
                ];
            })->filter(function ($item) {
                return $item->sales > 0 || $item->feed_cost > 0 || $item->expenses > 0;
            })->values();
        }

        return view('reports.index', compact('data', 'reportType'));
    }

    public function generateCustom(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'metrics' => 'required|array|min:1',
            'metrics.*' => 'in:eggs,sales,expenses',
            'format' => 'nullable|in:pdf,excel',
        ]);

        $data = [];

        if (in_array('eggs', $validated['metrics'])) {
            $data['eggs'] = Egg::whereBetween('date_laid', [$validated['start_date'], $validated['end_date']])
                ->orderBy('date_laid')
                ->get();
        }

        if (in_array('sales', $validated['metrics'])) {
            $data['sales'] = Sale::whereBetween('sale_date', [$validated['start_date'], $validated['end_date']])
                ->with(['customer', 'saleable'])
                ->orderBy('sale_date')
                ->get();
        }

        if (in_array('expenses', $validated['metrics'])) {
            $data['expenses'] = Expense::whereBetween('date', [$validated['start_date'], $validated['end_date']])
                ->orderBy('date')
                ->get();
        }

        $reportType = 'custom';

        if ($request->has('format')) {
            if ($validated['format'] === 'pdf') {
                $pdf = Pdf::loadView('reports.index_pdf', compact('data', 'reportType', 'validated'));
                return $pdf->download('analytics_report_' . now()->format('Ymd') . '.pdf');
            } elseif ($validated['format'] === 'excel') {
                return Excel::download(new CustomReportExport($data), 'analytics_report_' . now()->format('Ymd') . '.xlsx');
            }
        }

        return view('reports.index', compact('data', 'reportType', 'validated'));
    }

    public function export(Request $request)
    {
        $reportType = $request->input('type', 'weekly');
        $format = $request->input('format', 'pdf');
        $data = [];

        // Weekly report
        if ($reportType === 'weekly') {
            $data['weekly'] = DB::table('eggs')
                ->selectRaw('WEEK(date_laid, 1) as week, YEAR(date_laid) as year, SUM(sold_quantity) as total')
                ->where('date_laid', '>=', now()->subWeeks(8))
                ->groupBy('week', 'year')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc')
                ->get();
        }

        // Monthly report
        if ($reportType === 'monthly') {
            $data['monthly'] = Egg::whereBetween('date_laid', [now()->subMonths(6), now()])
                ->selectRaw('YEAR(date_laid) as year, MONTH(date_laid) as month_num, SUM(crates) as total')
                ->groupBy('year', 'month_num')
                ->orderBy('year', 'desc')
                ->orderBy('month_num', 'desc')
                ->get();
        }

        // Profitability report
        if ($reportType === 'profitability') {
            $birds = Bird::all();
            $data['profitability'] = $birds->map(function ($bird) {
                $sales = Sale::where('saleable_type', Bird::class)
                    ->where('saleable_id', $bird->id)
                    ->sum('total_amount') ?? 0;
                $feedCost = FeedConsumption::where('bird_id', $bird->id)
                    ->sum('cost') ?? 0;
                $expenses = Expense::where('bird_id', $bird->id)
                    ->sum('amount') ?? 0;

                return (object) [
                    'bird_id' => $bird->id,
                    'breed' => $bird->breed,
                    'sales' => $sales,
                    'feed_cost' => $feedCost,
                    'expenses' => $expenses,
                    'profit' => $sales - ($feedCost + $expenses),
                ];
            })->filter(function ($item) {
                return $item->sales > 0 || $item->feed_cost > 0 || $item->expenses > 0;
            })->values();
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.index_pdf', compact('data', 'reportType'));
            return $pdf->download("{$reportType}_report_" . now()->format('Ymd') . '.pdf');
        } elseif ($format === 'excel') {
            return Excel::download(new CustomReportExport($data), "{$reportType}_report_" . now()->format('Ymd') . '.xlsx');
        }

        return redirect()->route('reports.index', ['type' => $reportType]);
    }
}