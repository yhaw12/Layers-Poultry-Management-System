<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Bird;
use App\Models\Chicks;
use App\Models\Customer;
use App\Models\Egg;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Feed;
use App\Models\Income;
use App\Models\MedicineLog;
use App\Models\Mortalities;
use App\Models\Payroll;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with key metrics and trends.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Date filter defaults to current month
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());
        $period = [$start, $end];

        // Core financials
        $totalExpenses = Expense::whereBetween('date', $period)->sum('amount') ?? 0;
        $totalIncome = Income::whereBetween('date', $period)->sum('amount') ?? 0;
        $profit = $totalIncome - $totalExpenses;

        // Stock counts
        $chicks = Chicks::sum('quantity_bought') ?? 0;
        $layers = Bird::where('type', 'layer')->sum('quantity') ?? 0;
        $broilers = Bird::where('type', 'broiler')->sum('quantity') ?? 0;
        $totalBirds = $chicks + $layers + $broilers;

        $totalEggs = Egg::whereBetween('date_laid', [now()->startOfMonth(), now()->endOfMonth()])
        ->sum('crates') ?? 0;

        // Alerts for backup status
        $alerts = Alert::whereNull('read_at')
            ->whereIn('type', ['backup_success', 'backup_failed'])
            ->get();

        // Monthly KPIs
        $metrics = [
            'egg_crates' => Egg::whereBetween('date_laid', $period)->sum('crates') ?? 0,
            'feed_kg' => Feed::whereBetween('purchase_date', $period)->sum('quantity') ?? 0,
            'mortality' => Mortalities::whereBetween('date', $period)->sum('quantity') ?? 0,
            'medicine_buy' => MedicineLog::where('type', 'purchase')
                ->whereBetween('date', $period)
                ->sum('quantity') ?? 0,
            'medicine_use' => MedicineLog::where('type', 'consumption')
                ->whereBetween('date', $period)
                ->sum('quantity') ?? 0,
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

            // Fetch sales trend data, grouped by date
        $salesTrend = Sale::whereBetween('sale_date', [now()->subDays(30), now()])
            ->select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as value'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Render the dashboard view with all data
        return view('dashboard', compact(
            'start',
            'end',
            'totalExpenses',
            'totalIncome',
            'profit',
            'chicks',
            'layers',
            'broilers',
            'metrics',
            'mortalityRate',
            'fcr',
            'employees',
            'payroll',
            'eggTrend',
            'feedTrend',
            'payrollTrend',
            'alerts',
            'salesTrend'
        ));
    }

    /**
     * Export the dashboard data as a PDF.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportPDF(Request $request)
    {
        $data = $this->index($request)->getData();
        $pdf = Pdf::loadView('dashboard_pdf', $data);

        return $pdf->download('dashboard_report_' . now()->format('Ymd') . '.pdf');
    }
}