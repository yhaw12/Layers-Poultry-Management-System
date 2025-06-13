<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Exports\CustomReportExport;
use App\Models\Bird;
use App\Models\Egg;
use App\Models\Expense;
use App\Models\FeedConsumption;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->input('type', 'daily');
        $data = [];

        // Daily report
        if ($reportType === 'daily') {
            $data['daily'] = DB::table('eggs')
                ->selectRaw('DATE(date_laid) as date, COUNT(*) as total')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->take(10)
                ->get();
        }

        // Weekly report
        if ($reportType === 'weekly') {
            $data['weekly'] = DB::table('eggs')
                ->selectRaw('WEEK(date_laid, 1) as week, YEAR(date_laid) as year, COUNT(*) as total')
                ->groupBy('week', 'year')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc')
                ->take(8)
                ->get();
        }

        // Monthly report
        if ($reportType === 'monthly') {
            $data['monthly'] = DB::table('eggs')
                ->selectRaw('MONTHNAME(date_laid) as month, YEAR(date_laid) as year, COUNT(*) as total')
                ->groupBy('month', 'year')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->take(6)
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

                return [
                    'bird_id' => $bird->id,
                    'breed' => $bird->breed,
                    'sales' => $sales,
                    'feed_cost' => $feedCost,
                    'expenses' => $expenses,
                    'profit' => $sales - ($feedCost + $expenses),
                ];
            })->filter(function ($item) {
                return $item['sales'] > 0 || $item['feed_cost'] > 0 || $item['expenses'] > 0;
            })->values();
        }

        return view('reports.index', compact('data', 'reportType'));
    }

    public function generateCustom(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'metrics' => 'required|array',
            'metrics.*' => 'in:eggs,sales,expenses',
            'format' => 'required|in:pdf,excel,view',
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
                ->get();
        }
        if (in_array('expenses', $validated['metrics'])) {
            $data['expenses'] = Expense::whereBetween('date', [$validated['start_date'], $validated['end_date']])
                ->orderBy('date')
                ->get();
        }

        if ($validated['format'] === 'pdf') {
            return redirect()->route('reports.custom.pdf', array_merge($validated, ['data' => $data]));
        } elseif ($validated['format'] === 'excel') {
            return Excel::download(new CustomReportExport($data), 'report_custom_' . now()->format('Ymd') . '.xlsx');
        }

        return view('reports.index', [
            'data' => $data,
            'reportType' => 'custom',
            'validated' => $validated,
        ]);
    }

    public function generateCustomPDF(Request $request)
    {
        $data = $request->input('data', []);
        $validated = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'metrics' => $request->input('metrics', []),
        ];

        $pdf = Pdf::loadView('reports.custom_pdf', compact('data', 'validated'));
        return $pdf->download('custom_report_' . now()->format('Ymd') . '.pdf');
    }
}