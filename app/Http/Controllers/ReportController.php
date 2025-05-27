<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// class ReportController extends Controller
// {
//     public function daily()
//     {
//         // Example data fetch
//         $data = DB::table('eggs')
//             ->selectRaw('date(created_at) as date, sum(quantity) as total')
//             ->groupBy('date')
//             ->orderBy('date','desc')
//             ->take(7)
//             ->get();

//         return view('reports.daily', compact('data'));
//     }

//     public function weekly()
//     {
//         $data = DB::table('eggs')
//             ->selectRaw("week(created_at) as week, sum(quantity) as total")
//             ->groupBy('week')
//             ->orderBy('week','desc')
//             ->take(4)
//             ->get();

//         return view('reports.weekly', compact('data'));
//     }

//     public function monthly()
//     {
//         $data = DB::table('eggs')
//             ->selectRaw("month(created_at) as month, sum(quantity) as total")
//             ->groupBy('month')
//             ->orderBy('month','desc')
//             ->take(6)
//             ->get();

//         return view('reports.monthly', compact('data'));
//     }
// }


// app/Http/Controllers/ReportController.php
namespace App\Http\Controllers;

use App\Models\Egg;
use App\Models\Sale;
use App\Models\Expense;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomReportExport;
use App\Models\Bird;
use App\Models\FeedConsumption;

class ReportController extends Controller
{
    public function custom(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'metrics' => 'required|array', // e.g., ['eggs', 'sales', 'expenses']
            'format' => 'required|in:pdf,excel',
        ]);

        $data = [];
        if (in_array('eggs', $validated['metrics'])) {
            $data['eggs'] = Egg::whereBetween('date_laid', [$validated['start_date'], $validated['end_date']])
                               ->orderBy('date_laid')
                               ->get();
        }
        if (in_array('sales', $validated['metrics'])) {
            $data['sales'] = Sale::whereBetween('sale_date', [$validated['start_date'], $validated['end_date']])
                                 ->with('customer', 'saleable')
                                 ->get();
        }
        if (in_array('expenses', $validated['metrics'])) {
            $data['expenses'] = Expense::whereBetween('date', [$validated['start_date'], $validated['end_date']])
                                       ->get();
        }

        if ($validated['format'] === 'pdf') {
            $pdf = Pdf::loadView('reports.custom', compact('data', 'validated'));
            return $pdf->download('report_' . now()->format('Ymd') . '.pdf');
        }

        return Excel::download(new CustomReportExport($data), 'report.xlsx');
    }

    public function profitability(Request $request)
        {
            $birds = Bird::all();
            $profitData = $birds->map(function ($bird) {
                $sales = Sale::where('saleable_type', Bird::class)
                            ->where('saleable_id', $bird->id)
                            ->sum('total_amount');
                $feedCost = FeedConsumption::whereHas('feed', fn($q) => $q->where('bird_id', $bird->id))
                            ->sum('cost');
                $expenses = Expense::where('bird_id', $bird->id)->sum('amount');
                return [
                    'bird' => $bird->id,
                    'breed' => $bird->breed,
                    'profit' => $sales - ($feedCost + $expenses),
                ];
            });
            return view('reports.profitability', compact('profitData'));
        }
}