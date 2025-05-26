<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\{
    Expense,
    Income,
    Chicks,
    Bird,
    Feed,
    Egg,
    Mortalities,
    Employee,
    MedicineLog,
    Sale,
    Customer,
    Payroll
};

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Date filter defaults to current month
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());

        // Core financials
        $totalExpenses = Expense::whereBetween('date', [$start, $end])->sum('amount') ?? 0;
        $totalIncome = Income::whereBetween('date', [$start, $end])->sum('amount') ?? 0;
        $profit = $totalIncome - $totalExpenses;

        // Stock counts
        $chicks = Chicks::sum('quantity_bought') ?? 0;
        $layers = Bird::where('type', 'layer')->sum('quantity') ?? 0;
        $broilers = Bird::where('type', 'broiler')->sum('quantity') ?? 0;

        // Total flock for mortality calculation
        $totalBirds = $chicks + $layers + $broilers;

        // Timeframe for trends
        $period = [$start, $end];

        // Monthly KPIs
        $metrics = [
            'egg_crates' => Egg::whereBetween('date_laid', $period)->sum('crates') ?? 0,
            'feed_kg' => Feed::whereBetween('purchase_date', $period)->sum('quantity') ?? 0,
            'mortality' => Mortalities::whereBetween('date', $period)->sum('quantity') ?? 0,
            'medicine_buy' => MedicineLog::where('type', 'purchase')->whereBetween('date', $period)->sum('quantity') ?? 0,
            'medicine_use' => MedicineLog::where('type', 'consumption')->whereBetween('date', $period)->sum('quantity') ?? 0,
            'sales' => Sale::whereBetween('sale_date', $period)
                ->selectRaw('SUM(quantity * unit_price) as total')
                ->value('total') ?? 0,
            'customers' => Customer::count() ?? 0,
        ];

        // Calculated KPIs
        $mortalityRate = $totalBirds ? ($metrics['mortality'] / $totalBirds) * 100 : 0;
        $fcr = $metrics['egg_crates'] ? round($metrics['feed_kg'] / $metrics['egg_crates'], 2) : 0;

        // Employee overview
        $employees = Employee::count() ?? 0;
        $payroll = Payroll::whereBetween('pay_date', $period)->sum('net_pay') ?? 0;

        // Trend data for charts
        $eggTrend = Egg::whereBetween('date_laid', $period)
            ->select(DB::raw('date_laid as date'), DB::raw('SUM(crates) as value'))
            ->groupBy('date_laid')
            ->orderBy('date_laid')
            ->get();
        $feedTrend = Feed::whereBetween('purchase_date', $period)
            ->select(DB::raw('purchase_date as date'), DB::raw('SUM(quantity) as value'))
            ->groupBy('purchase_date')
            ->orderBy('purchase_date')
            ->get();
        $payrollTrend = Payroll::whereBetween('pay_date', $period)
            ->select(DB::raw('pay_date as date'), DB::raw('SUM(net_pay) as value'))
            ->groupBy('pay_date')
            ->orderBy('pay_date')
            ->get();

        return view('dashboard', compact(
            'start', 'end',
            'totalExpenses', 'totalIncome', 'profit',
            'chicks', 'layers', 'broilers',
            'metrics', 'mortalityRate', 'fcr',
            'employees', 'payroll',
            'eggTrend', 'feedTrend', 'payrollTrend'
        ));
    }

    public function exportPDF(Request $request)
    {
        $data = $this->index($request)->getData();
        $pdf = Pdf::loadView('dashboard_pdf', $data);

        return $pdf->download('dashboard_report_' . now()->format('Ymd') . '.pdf');
    }
}