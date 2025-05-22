<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Chicks; 
use App\Models\Hen;
use App\Models\Feed;
use App\Models\Egg;
use App\Models\Death;
use App\Models\Employee;


class DashboardController extends Controller
{
    public function index(Request $request)
{
    $start_date = $request->input('start_date', now()->startOfMonth()->toDateString());
    $end_date = $request->input('end_date', now()->endOfMonth()->toDateString());

    $totalExpenses = Expense::whereBetween('date', [$start_date, $end_date])->sum('amount');
    $totalIncome = Income::whereBetween('date', [$start_date, $end_date])->sum('amount');
    $profit = $totalIncome - $totalExpenses;

    $chickCount = Chicks::sum('quantity_bought');
    $henCount = Hen::sum('quantity');
    $totalBirds = $chickCount + $henCount;

    $currentMonth = now()->month;
    $currentYear = now()->year;

    $eggCountThisMonth = Egg::whereMonth('date_laid', $currentMonth)
                            ->whereYear('date_laid', $currentYear)
                            ->sum('crates');
    $feedQuantityThisMonth = Feed::whereMonth('purchase_date', $currentMonth)
                                ->whereYear('purchase_date', $currentYear)
                                ->sum('quantity');

    // Mortality
    $mortalityCount = Death::whereMonth('date', $currentMonth)
                            ->whereYear('date', $currentYear)
                            ->sum('quantity'); // Adjust field name if needed
    $mortalityRate = $totalBirds > 0 ? ($mortalityCount / $totalBirds) * 100 : 0;

    // Employees
    $employeeCount = Employee::count();
    $monthlyPayroll = Employee::sum('monthly_salary'); // Replace with actual column name

    return view('dashboard', compact(
        'totalExpenses',
        'totalIncome',
        'profit',
        'chickCount',
        'henCount',
        'feedQuantityThisMonth',
        'eggCountThisMonth',
        'mortalityRate',
        'employeeCount',
        'monthlyPayroll',
        'start_date',
        'end_date'
    ));
}

}