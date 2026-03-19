<?php

namespace App\Http\Controllers;

use App\Models\Egg;
use App\Models\Pen;
use App\Models\UserActivityLog;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class EggController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date|before_or_equal:end_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $start = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : now()->subMonths(6)->startOfMonth()->startOfDay();
        $end = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfMonth()->endOfDay();

        $query = Egg::query()->with(['pen', 'createdBy'])->whereBetween('date_laid', [$start, $end]);

        if ($search = $request->input('search')) {
            $query->where('date_laid', 'like', "%{$search}%")
                ->orWhere('crates', 'like', "%{$search}%")
                ->orWhereHas('pen', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('createdBy', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        $eggs = $query->orderBy('date_laid', 'desc')->paginate(10);

        // --- CONSOLIDATED LOGIC (Renamed to match Blade) ---
        
        // 1. Total Eggs Produced (Within Date Filter for Reports)
        $totalProducedEggs = (int) Egg::whereBetween('date_laid', [$start, $end])->sum('total_eggs');
        $totalProducedCrates = floor($totalProducedEggs / 30);
        $totalAdditionalEggs = $totalProducedEggs % 30;

        // 2. Cracked Eggs (Within Date Filter)
        $totalCracked = (int) Egg::whereBetween('date_laid', [$start, $end])->where('is_cracked', true)->count();

        // 3. Total Sold Crates (Within Date Filter)
        $totalSoldCrates = (int) SaleItem::where('saleable_type', Egg::class)
            ->whereHas('sale', function ($q) use ($start, $end) {
                $q->whereBetween('sale_date', [$start, $end]);
            })
            ->sum('quantity');

        // 4. REAL-TIME REMAINING STOCK (Matches Sales Dropdown Exactly)
        // We fetch ALL batches regardless of date, calculate their true remaining balance, and sum it up.
        // $allBatches = Egg::withSum('saleItems as sold_crates', 'quantity')->get();
        $availableBatches = Egg::available()->get();
        
        $remainingCrates = 0;
        $totalLooseEggs = 0;

        foreach ($availableBatches as $batch) {
            // Add up the remaining crates from each batch
            $remainingCrates += max(0, $batch->crates - ($batch->sold_crates ?? 0));
            // Add up the loose eggs
            $totalLooseEggs += $batch->additional_eggs;
        }

        // Convert any accumulated loose eggs into full crates (e.g., 40 loose eggs = 1 crate + 10 eggs)
        $remainingCrates += floor($totalLooseEggs / 30);
        $remainingEggs = $totalLooseEggs % 30;


        $eggChart = Cache::remember('egg_trends', 3, function () {
            $data = [];
            $labels = [];
            for ($i = 0; $i < 6; $i++) {
                $month = now()->subMonths($i);
                $labels[] = $month->format('M Y');
                $data[] = (int) Egg::whereMonth('date_laid', $month->month)
                    ->whereYear('date_laid', $month->year)
                    ->sum('crates');
            }
            return ['data' => array_reverse($data), 'labels' => array_reverse($labels)];
        });

        $eggLabels = $eggChart['labels'];
        $eggData = $eggChart['data'];

        return view('eggs.index', compact(
            'eggs',
            'totalProducedEggs',
            'totalProducedCrates',
            'totalAdditionalEggs',
            'totalCracked',
            'totalSoldCrates',
            'remainingCrates',
            'remainingEggs',
            'eggLabels',
            'eggData',
            'start',
            'end'
        ));
    }

    public function create()
    {
        $pens = Pen::orderBy('name')->get(['id', 'name']);
        return view('eggs.create', compact('pens'));
    }

    public function store(Request $request)
    {
    $rules = [
        'pen_id' => 'nullable|exists:pens,id',
        'crates' => 'required|integer|min:0',
        'additional_eggs' => 'required|integer|min:0|max:29',
        'is_cracked' => 'nullable|boolean',
        'egg_size' => 'nullable|in:small,medium,large',
        'date_laid' => 'required|date|before_or_equal:today',
    ];

    $validated = $request->validate($rules);

    // --- FIX: Explicit Assignment ---
    // We pull directly from the request to ensure the integer values are captured
    $crates = (int) $request->input('crates');
    $additional = (int) $request->input('additional_eggs');

    $validated['crates'] = $crates;
    $validated['additional_eggs'] = $additional;
    $validated['is_cracked'] = $request->has('is_cracked');
    $validated['total_eggs'] = ($crates * 30) + $additional;
    $validated['created_by'] = auth()->id();

    DB::beginTransaction();
    try {
        $egg = Egg::create($validated);

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created_egg',
            'details' => "Created egg record #{$egg->id} with {$crates} crates and {$additional} loose eggs (Pen ID: " . ($validated['pen_id'] ?? 'null') . ")",
        ]);

        DB::commit();

        return redirect()->route('eggs.index')->with('success', 'Egg record added successfully');
    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Failed to create egg record', [
            'error' => $e->getMessage(),
            'request' => $request->all(),
        ]);

        return back()->withInput()->with('error', 'Failed to add egg record: ' . $e->getMessage());
    }
    }

    public function edit(Egg $egg)
    {
        $pens = Pen::orderBy('name')->get(['id', 'name']);
        return view('eggs.edit', compact('egg', 'pens'));
    }

    public function update(Request $request, Egg $egg)
    {
        $rules = [
            'pen_id' => 'nullable|exists:pens,id',
            'crates' => 'required|integer|min:0',
            'additional_eggs' => 'required|integer|min:0|max:29',
            'is_cracked' => 'nullable|boolean',
            'egg_size' => 'nullable|in:small,medium,large',
            'date_laid' => 'required|date|before_or_equal:today',
        ];

        $validated = $request->validate($rules);

        // --- FIX: Explicit Assignment ---
        // Extracting specifically to ensure the "0 crates" bug is killed
        $crates = (int) $request->input('crates');
        $additional = (int) $request->input('additional_eggs');

        $validated['crates'] = $crates;
        $validated['additional_eggs'] = $additional;
        $validated['is_cracked'] = $request->has('is_cracked');
        $validated['total_eggs'] = ($crates * 30) + $additional;
        
        // Tracking who last modified the record
        $validated['created_by'] = auth()->id(); 

        DB::beginTransaction();
        try {
            $egg->update($validated);

            UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'updated_egg',
                'details' => "Updated egg record #{$egg->id}: Set to {$crates} crates and {$additional} loose eggs (Size: " . ($validated['egg_size'] ?? 'N/A') . ")",
            ]);

            DB::commit();

            return redirect()->route('eggs.index')->with('success', 'Egg record updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update egg record', [
                'error' => $e->getMessage(),
                'egg_id' => $egg->id,
                'request' => $request->all(),
            ]);

            return back()->withInput()->with('error', 'Failed to update egg record: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $egg = Egg::findOrFail($id);
            $egg->delete();

            UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'deleted_egg',
                'details' => "Deleted egg record #{$egg->id} with {$egg->crates} crates on {$egg->date_laid}",
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Egg deleted successfully.'
                ], 200);
            }

            return redirect()->route('eggs.index')->with('success', 'Egg deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete egg record', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'egg_id' => $id,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete egg record. ' . ($e->getCode() == 23000 ? 'This record is linked to other data.' : 'Please try again.')
                ], 500);
            }

            return redirect()->route('eggs.index')->with('error', 'Failed to delete egg record.');
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            Egg::whereIn('id', $ids)->delete();

            UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'bulk_deleted_eggs',
                'details' => "Deleted " . count($ids) . " egg records",
            ]);
        }

        return redirect()->route('eggs.index')->with('success', 'Selected records deleted.');
    }

    public function show(Egg $egg)
    {
        $egg->load(['pen', 'createdBy']);
        return view('eggs.show', compact('egg'));
    }
}
