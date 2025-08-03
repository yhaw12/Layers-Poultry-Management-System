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
            $query = Egg::query()->with(['pen', 'collectedBy']);
            if ($search = $request->input('search')) {
                $query->where('date_laid', 'like', "%{$search}%")
                      ->orWhere('crates', 'like', "%{$search}%")
                      ->orWhereHas('pen', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('collectedBy', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            }

            $eggs = $query->orderBy('date_laid', 'desc')->paginate(10);

            $totalCrates = Egg::sum('crates') ?? 0;
            $totalProduced = $totalCrates;
            $totalCracked = Egg::sum('cracked_eggs') ?? 0;

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
                'small_eggs' => 'required|integer|min:0',
                'medium_eggs' => 'required|integer|min:0',
                'large_eggs' => 'required|integer|min:0',
                'cracked_eggs' => 'required|integer|min:0',
                'collected_by' => 'required|exists:users,id',
                'date_laid' => 'required|date',
            ]);

            $total_eggs = $data['small_eggs'] + $data['medium_eggs'] + $data['large_eggs'];
            $data['crates'] = round($total_eggs / 30, 2);
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
                'small_eggs' => 'required|integer|min:0',
                'medium_eggs' => 'required|integer|min:0',
                'large_eggs' => 'required|integer|min:0',
                'cracked_eggs' => 'required|integer|min:0',
                'collected_by' => 'required|exists:users,id',
                'date_laid' => 'required|date',
            ]);

            $total_eggs = $data['small_eggs'] + $data['medium_eggs'] + $data['large_eggs'];
            $data['crates'] = round($total_eggs / 30, 2);
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
            $egg->load(['pen', 'collectedBy']);
            return view('eggs.show', compact('egg'));
        }
    }
    