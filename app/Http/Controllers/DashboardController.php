<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Chicks; // Corrected from Chicks
use App\Models\Hen;
use App\Models\Feed;
use App\Models\Egg;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end_date = $request->input('end_date', now()->endOfMonth()->toDateString());

        $totalExpenses = Expense::whereBetween('date', [$start_date, $end_date])->sum('amount');
        $totalIncome = Income::whereBetween('date', [$start_date, $end_date])->sum('amount');
        $profit = $totalIncome - $totalExpenses;

        // Use correct column names based on migrations
        $chickCount = Chicks::sum('quantity_bought'); // Matches 'quantity_bought' from chicks migration
        $henCount = Hen::sum('quantity');            // Matches 'quantity' from hens migration

        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Use 'crates' instead of 'quantity' for eggs
        $eggCountThisMonth = Egg::whereMonth('date_laid', $currentMonth)
                                ->whereYear('date_laid', $currentYear)
                                ->sum('crates'); // Updated to match eggs migration
        $feedQuantityThisMonth = Feed::whereMonth('purchase_date', $currentMonth)
                                    ->whereYear('purchase_date', $currentYear)
                                    ->sum('quantity'); // Matches 'quantity' from feed migration

        return view('dashboard', compact(
            'totalExpenses',
            'totalIncome',
            'profit',
            'chickCount',
            'henCount',
            'feedQuantityThisMonth',
            'eggCountThisMonth',
            'start_date',
            'end_date'
        ));
    }
}