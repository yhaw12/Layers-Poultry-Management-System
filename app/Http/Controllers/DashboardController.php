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
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the dashboard with key metrics and trends.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {

        $eggTrend = Cache::remember('egg_trend', 3600, function () {
            return Egg::selectRaw('DATE(date_laid) as date, SUM(quantity) as total')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(7)
                ->get();
        });

        // Date filter defaults to current month
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());
        $period = [$start, $end];

        // Get authenticated user with null check
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $isAdmin = $user->isAdmin();

        // Common data for all users
        $chicks = Chicks::sum('quantity_bought') ?? 0;
        $layers = Bird::where('type', 'layer')->sum('quantity') ?? 0;
        $broilers = Bird::where('type', 'broiler')->sum('quantity') ?? 0;
        $totalBirds = $chicks + $layers + $broilers;

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
        ];

        $mortalityRate = $totalBirds ? ($metrics['mortality'] / $totalBirds) * 100 : 0;
        $fcr = $metrics['egg_crates'] ? round($metrics['feed_kg'] / $metrics['egg_crates'], 2) : 0;

        $eggTrend = Egg::whereBetween('date_laid', $period)
            ->select(DB::raw('date_laid as date'), DB::raw('SUM(crates) as value'))
            ->groupBy('date_laid')
            ->orderBy('date_laid')
            ->take(50)
            ->get();

        $feedTrend = Feed::whereBetween('purchase_date', $period)
            ->select(DB::raw('purchase_date as date'), DB::raw('SUM(quantity) as value'))
            ->groupBy('purchase_date')
            ->orderBy('purchase_date')
            ->take(50)
            ->get();

        // Admin-only data
        if ($isAdmin) {
            $totalExpenses = Expense::whereBetween('date', $period)->sum('amount') ?? 0;
            $totalIncome = Income::whereBetween('date', $period)->sum('amount') ?? 0;
            $profit = $totalIncome - $totalExpenses;

            $metrics['sales'] = Sale::whereBetween('sale_date', $period)
                ->selectRaw('SUM(total_amount) as total')
                ->value('total') ?? 0;
            $metrics['customers'] = Customer::count() ?? 0;

            $employees = Employee::count() ?? 0;
            $payroll = Payroll::whereBetween('pay_date', $period)->sum('net_pay') ?? 0;

            $salesTrend = Sale::whereBetween('sale_date', $period)
                ->select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as value'))
                ->groupBy('date')
                ->orderBy('date')
                ->take(50)
                ->get();

            $payrollTrend = Payroll::whereBetween('pay_date', $period)
                ->select(DB::raw('pay_date as date'), DB::raw('SUM(net_pay) as value'))
                ->groupBy('pay_date')
                ->orderBy('pay_date')
                ->take(50)
                ->get();

            $alerts = Alert::where('user_id', $user->id)->whereNull('read_at')->take(50)->get();

            return view('dashboard.admin', compact(
                'start', 'end', 'totalExpenses', 'totalIncome', 'profit',
                'chicks', 'layers', 'broilers', 'metrics', 'mortalityRate', 'fcr',
                'employees', 'payroll', 'eggTrend', 'feedTrend', 'salesTrend', 'payrollTrend', 'alerts','eggTrend'
            ));
        }

        // Non-admin view
        return view('dashboard.user', compact(
            'start', 'end', 'chicks', 'layers', 'broilers',
            'metrics', 'mortalityRate', 'fcr', 'eggTrend', 'feedTrend'
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
        /** @var User $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $data = $this->index($request)->getData();
        $pdf = Pdf::loadView('dashboard_pdf', $data);
        return $pdf->download('dashboard_report_' . now()->format('Ymd') . '.pdf');
    }
}