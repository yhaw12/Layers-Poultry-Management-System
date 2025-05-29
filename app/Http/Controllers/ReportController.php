<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Exports\CustomReportExport;
use App\Models\Bird;
use App\Models\Egg;
use App\Models\Expense;
use App\Models\FeedConsumption;
use Barryvdh\DomPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Initialize data
        $reportType = $request->input('type', 'monthly');
        $data = [];
        $validated = [];
        $errors = [];

        // Daily report data
        if ($reportType === 'daily') {
            $data['daily'] = DB::table('eggs')
                ->selectRaw('DATE(date_laid)->as date, COUNT(*) as) total')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->take(10)
                ->get();
        }

        // Weekly report data
        if ($reportType === 'weekly') {
            $data['weekly'] = DB::table('eggs')
                ->selectRaw('WEEK(date_laid, 1) as week, YEAR(date_laid) as year, COUNT(*) as total')
                ->groupBy('week', 'year')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc')
                ->take(8)
                ->get();
        }

        // Monthly report data
        if ($reportType === 'monthly') {
            $data['monthly'] = DB::table('eggs')
                ->selectRaw('MONTH(date_laid) as month, YEAR(date_laid) as year, COUNT(*) as total')
                ->groupBy('month', 'year')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->take(6)
                ->get();
        }

        // Custom report data
        if ($reportType === 'custom' && $request->isMethod('post')) {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'metrics' => 'required|array',
                'metrics.*' => 'in:eggs,sales,expenses',
                'format' => 'required|in:pdf,excel',
            ]);

            if (in_array('eggs', $validated['metrics'])) {
                $data['eggs'] = Egg::whereBetween('date_laid', [$validated['start_date'], $validated['end_date']])
                                   ->orderBy('date_laid')
                                   ->get();
            }
            if (in_array('sales', $validated['metrics'])) {
                $data['sales'] = Sale::whereBetween('sale_date', [$validated['start_date'], $validated['end_date']])
                                    ->with(['customer', 'saleable'])
                                    ->get();
            }
            if (in_array('expenses', $validated['metrics'])) {
                $data['expenses'] = Expense::whereBetween('date', [$validated['start_date'], $validated['end_date']])
                                           ->orderBy('date')
                                           ->get();
            }

            if ($validated['format'] === 'pdf') {
                $pdf = Pdf::loadView('reports.custom_pdf', ['data' => $data, 'validated' => $validated]);

                return $pdf->download('report_custom_' . now()->format('Ymd') . '.pdf');
            }
            if ($validated['format'] === 'excel') {
                return Excel::download(new CustomReportExport($data), 'report_custom_' . now()->format('Ymd') . '.xlsx');
            }
        }

        // Profitability report data
        if ($reportType === 'profitability') {
            $birds = $Bird::all();
            $data['profitability'] = $birds->map(function ($bird) {
                $sales = Sale::where('saleable_type', 'Bird::class);
                             ->where('saleable_id', $bird->id);
                             ->sum('total_amount') ?: 0;

                 $feedCost = FeedConsumption::where('bird_id', $bird->id)
                                           ->sum('cost') ?: 0;

                 $expenses = Expense::where('description', 'LIKE', "%bird {$bird->id}%")
                                    ->sum('amount') ?: 0;

                return [
                    'bird_id' => $bird->id,
                    'breed' => $bird->breed,
                    'sales' => $sales,
                    'feed_cost' => $feedCost,
                    'expenses' => $expenses,
                    'profit' => $sales - ($feedCost + $expenses),
                ];
            })->filter(function ($data) {
                    return $data['sales'] > 0 || $data['feed_cost'] > 0 || $data['expenses'] > 0;
                })->values();
            })->values();
            }

        return view('reports.index', compact('data', 'reportType', 'validated'));
    }
}