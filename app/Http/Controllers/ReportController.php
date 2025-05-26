<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function daily()
    {
        // Example data fetch
        $data = DB::table('eggs')
            ->selectRaw('date(created_at) as date, sum(quantity) as total')
            ->groupBy('date')
            ->orderBy('date','desc')
            ->take(7)
            ->get();

        return view('reports.daily', compact('data'));
    }

    public function weekly()
    {
        $data = DB::table('eggs')
            ->selectRaw("week(created_at) as week, sum(quantity) as total")
            ->groupBy('week')
            ->orderBy('week','desc')
            ->take(4)
            ->get();

        return view('reports.weekly', compact('data'));
    }

    public function monthly()
    {
        $data = DB::table('eggs')
            ->selectRaw("month(created_at) as month, sum(quantity) as total")
            ->groupBy('month')
            ->orderBy('month','desc')
            ->take(6)
            ->get();

        return view('reports.monthly', compact('data'));
    }
}
