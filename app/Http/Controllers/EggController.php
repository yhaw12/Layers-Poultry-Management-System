<?php

namespace App\Http\Controllers;

use App\Models\Egg;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EggController extends Controller
{
    /**
     * Display a listing of egg records with search and chart data.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
    // Search query
    $query = Egg::query();
    if ($search = $request->input('search')) {
        $query->where('date_laid', 'like', "%{$search}%")
            ->orWhere('crates', 'like', "%{$search}%");
    }

    // Paginated eggs
    $eggs = $query->orderBy('date_laid', 'desc')->paginate(10);

    // Summary statistics
    $totalCrates = Egg::sum('crates') ?? 0;
    $totalSold = Egg::sum('sold_quantity') ?? 0;
    $totalProduced = $totalCrates;

    // Monthly egg crate chart data (last 6 months, cached for 1 hour)
    $eggChart = Cache::remember('egg_trends', 3600, function () {
        $data = [];
        $labels = [];
        for ($i = 0; $i < 6; $i++) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $data[] = Egg::whereMonth('date_laid', $month->month)
                ->whereYear('date_laid', $month->year)
                ->sum('crates') ?? 0;
        }
        return ['data' => array_reverse($data), 'labels' => array_reverse($labels)];
    });
        // Extract labels and data
        $eggLabels = $eggChart['labels'];
        $eggData = $eggChart['data'];

        return view('eggs.index', compact(
            'eggs',
            'totalCrates',
            'totalSold',
            'totalProduced',
            'eggLabels',
            'eggData'
        ));
    }

    /**
     * Show the form for creating a new egg record.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('eggs.create');
    }

    /**
     * Store a newly created egg record.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'crates' => 'required|integer|min:0',
            'date_laid' => 'required|date',
            'sold_quantity' => 'nullable|integer|min:0|lte:crates',
            'sold_date' => 'nullable|date',
            'sale_price' => 'nullable|numeric|min:0',
        ]);

        $egg = Egg::create($data);

        UserActivityLog::create([
            'user_id' => auth()->id,
            'action' => 'created_egg',
            'details' => "Created egg record with {$data['crates']} crates on {$data['date_laid']}",
        ]);

        return redirect()->route('eggs.index')->with('success', 'Egg record added successfully');
    }

    /**
     * Show the form for editing an egg record.
     *
     * @param Egg $egg
     * @return \Illuminate\View\View
     */
    public function edit(Egg $egg)
    {
        return view('eggs.edit', compact('egg'));
    }

    /**
     * Update an existing egg record.
     *
     * @param Request $request
     * @param Egg $egg
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Egg $egg)
    {
        $data = $request->validate([
            'crates' => 'required|integer|min:0',
            'date_laid' => 'required|date',
            'sold_quantity' => 'nullable|integer|min:0|lte:crates',
            'sold_date' => 'nullable|date',
            'sale_price' => 'nullable|numeric|min:0',
        ]);

        $egg->update($data);

        UserActivityLog::create([
            'user_id' => auth()->id,
            'action' => 'updated_egg',
            'details' => "Updated egg record with {$data['crates']} crates on {$data['date_laid']}",
        ]);

        return redirect()->route('eggs.index')->with('success', 'Egg record updated successfully');
    }

    /**
     * Soft delete an egg record.
     *
     * @param Egg $egg
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Egg $egg)
    {
        $egg->delete();

        UserActivityLog::create([
            'user_id' => auth()->id,
            'action' => 'deleted_egg',
            'details' => "Deleted egg record with {$egg->crates} crates on {$egg->date_laid}",
        ]);

        return redirect()->route('eggs.index')->with('success', 'Egg record deleted successfully');
    }

    /**
     * Soft delete multiple egg records.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            Egg::whereIn('id', $ids)->delete();

            UserActivityLog::create([
                'user_id' => auth()->id,
                'action' => 'bulk_deleted_eggs',
                'details' => "Deleted " . count($ids) . " egg records",
            ]);
        }

        return redirect()->route('eggs.index')->with('success', 'Selected records deleted.');
    }
    /**
 * Display a single egg record.
 *
 * @param Egg $egg
 * @return \Illuminate\View\View
 */
    public function show(Egg $egg)
        {
            return view('eggs.show', compact('egg'));
        }
}