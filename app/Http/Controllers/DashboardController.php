<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\{
    Expense,
    Income,
    Chicks,
    Hen,
    Broiler,
    Feed,
    Egg,
    Mortalities,
    Employee,
    MedicineLog,
    Sale,
    Customer
};

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Date filter defaults to current month
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end   = $request->input('end_date', now()->endOfMonth()->toDateString());

        // Core financials
        $totalExpenses = Expense::whereBetween('date', [$start, $end])->sum('amount');
        $totalIncome   = Income::whereBetween('date', [$start, $end])->sum('amount');
        $profit        = $totalIncome - $totalExpenses;

        // Stock counts
        $chicks   = Chicks::sum('quantity_bought');
        $layers   = Hen::sum('quantity');
        $broilers = Broiler::sum('quantity');
        $birds    = $chicks + $layers + $broilers;

        // Timeframe for trends
        $period = [$start, $end];

        // Monthly KPIs
        $metrics = [
            'egg_crates'    => Egg::whereBetween('date_laid', $period)->sum('crates'),
            'feed_kg'       => Feed::whereBetween('purchase_date', $period)->sum('quantity'),
            'mortality'     => Mortalities::whereBetween('date', $period)->sum('quantity'),
            'medicine_buy'  => MedicineLog::where('type', 'purchase')->whereBetween('date', $period)->sum('quantity'),
            'medicine_use'  => MedicineLog::where('type', 'consumption')->whereBetween('date', $period)->sum('quantity'),
            'sales'         => Sale::whereBetween('date_sold', $period)
                                ->selectRaw('SUM(quantity * unit_price) as total')
                                ->value('total'),
            'customers'     => Customer::count(),
        ];

        // Calculated KPIs
        $mortalityRate = $birds ? ($metrics['mortality'] / $birds) * 100 : 0;
        $fcr           = $metrics['egg_crates'] ? round($metrics['feed_kg'] / $metrics['egg_crates'], 2) : null;

        // Employee overview
        $employees     = Employee::count();
        $payroll       = Employee::sum('monthly_salary');

        // Trend data for charts
        $eggTrend  = Egg::whereBetween('date_laid', $period)
            ->select(DB::raw('date_laid as date'), DB::raw('SUM(crates) as value'))
            ->groupBy('date_laid')
            ->orderBy('date_laid')
            ->get();
        $feedTrend = Feed::whereBetween('purchase_date', $period)
            ->select(DB::raw('purchase_date as date'), DB::raw('SUM(quantity) as value'))
            ->groupBy('purchase_date')
            ->orderBy('purchase_date')
            ->get();

        return view('dashboard', compact(
            'start', 'end', 'totalExpenses', 'totalIncome', 'profit',
            'chicks', 'layers', 'broilers', 'birds',
            'metrics', 'mortalityRate', 'fcr',
            'employees', 'payroll', 'eggTrend', 'feedTrend'
        ));
    }

    public function exportPDF(Request $request)
    {
        $data = $this->index($request)->getData();
        $pdf = Pdf::loadView('dashboard_pdf', $data);

        return $pdf->download('dashboard_report_' . now()->format('Ymd') . '.pdf');
    }
}
