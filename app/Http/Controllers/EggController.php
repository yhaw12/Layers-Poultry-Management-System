<?php

namespace App\Http\Controllers;

use App\Models\Egg;
use App\Models\Pen;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EggController extends Controller
{
    public function index(Request $request)
    {
        $query = Egg::query()->with(['pen', 'createdBy']);
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

        $totalCrates = Egg::sum('crates') ?? 0;
        $totalProduced = Egg::sum('total_eggs') ?? 0;
        $totalCracked = Egg::where('is_cracked', true)->count() ?? 0; // Updated since no cracked_eggs

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

        $eggLabels = $eggChart['labels'];
        $eggData = $eggChart['data'];

        return view('eggs.index', compact(
            'eggs',
            'totalCrates',
            'totalProduced',
            'totalCracked',
            'eggLabels',
            'eggData'
        ));
    }

    public function create()
    {
        $pens = Pen::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name']);
        return view('eggs.create', compact('pens', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pen_id' => 'required|exists:pens,id',
            'crates' => 'required|numeric|min:0',
            'additional_eggs' => 'required|integer|min:0|max:29',
            'is_cracked' => 'nullable|boolean',
            'egg_size' => 'nullable|in:small,medium,large',
            'date_laid' => 'nullable|date',
        ]);

        $data['total_eggs'] = (int)($data['crates'] * 30) + $data['additional_eggs'];
        $data['is_cracked'] = $request->has('is_cracked');
        $data['created_by'] = auth()->id();

        $egg = Egg::create($data);

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created_egg',
            'details' => "Created egg record with {$data['crates']} crates (Pen ID: {$data['pen_id']}) on {$data['date_laid']}",
        ]);

        return redirect()->route('eggs.index')->with('success', 'Egg record added successfully');
    }

    public function edit(Egg $egg)
    {
        $pens = Pen::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name']);
        return view('eggs.edit', compact('egg', 'pens', 'users'));
    }

    public function update(Request $request, Egg $egg)
    {
        $data = $request->validate([
            'pen_id' => 'required|exists:pens,id',
            'crates' => 'required|numeric|min:0',
            'additional_eggs' => 'required|integer|min:0|max:29',
            'is_cracked' => 'nullable|boolean',
            'egg_size' => 'nullable|in:small,medium,large',
            'date_laid' => 'required|date',
        ]);

        $data['total_eggs'] = (int)($data['crates'] * 30) + $data['additional_eggs'];
        $data['is_cracked'] = $request->has('is_cracked');
        $data['created_by'] = auth()->id();

        $egg->update($data);

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated_egg',
            'details' => "Updated egg record with {$data['crates']} crates (Pen ID: {$data['pen_id']}) on {$data['date_laid']}",
        ]);

        return redirect()->route('eggs.index')->with('success', 'Egg record updated successfully');
    }

    public function destroy(Egg $egg)
    {
        $egg->delete();

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted_egg',
            'details' => "Deleted egg record with {$egg->crates} crates on {$egg->date_laid}",
        ]);

        return redirect()->route('eggs.index')->with('success', 'Egg record deleted successfully');
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