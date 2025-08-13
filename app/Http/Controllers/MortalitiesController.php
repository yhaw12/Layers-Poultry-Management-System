<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Mortalities;
use App\Models\Bird;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MortalitiesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'nullable|date|before_or_equal:end_date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());
            $cacheKey = "mortalities_{$start}_{$end}";

            $mortalities = Cache::remember($cacheKey, 300, function () use ($start, $end) {
                return Mortalities::with('bird')
                    ->whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->orderBy('date', 'desc')
                    ->paginate(10);
            });

            $totalMortalities = Cache::remember("total_mortalities_{$start}_{$end}", 300, function () use ($start, $end) {
                return Mortalities::whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('quantity') ?? 0;
            });

            return view('mortalities.index', compact('mortalities', 'totalMortalities', 'start', 'end'));
        } catch (\Exception $e) {
            Log::error('Failed to load mortalities', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load mortalities.');
        }
    }

    public function create()
    {
        $birds = Bird::whereNull('deleted_at')->get();
        return view('mortalities.create', compact('birds'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'bird_id' => 'required|exists:birds,id',
                'date' => 'required|date',
                'quantity' => 'required|integer|min:1',
                'cause' => 'nullable|string|max:255',
            ]);

            $mortality = Mortalities::create($validated);

            $bird = Bird::find($validated['bird_id']);
            if ($bird->stage === 'chick') {
                $totalDead = Mortalities::where('bird_id', $bird->id)
                    ->whereNull('deleted_at')
                    ->sum('quantity');
                $bird->dead = $totalDead;
                $bird->save();
            }

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'created_mortality',
                'details' => "Recorded {$validated['quantity']} mortalities for bird ID {$validated['bird_id']} on {$validated['date']}",
            ]);

            return redirect()->route('mortalities.index')->with('success', 'Mortality record added successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to store mortality', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to add mortality record.');
        }
    }

    public function edit(Mortalities $mortality)
    {
        $birds = Bird::whereNull('deleted_at')->get();
        return view('mortalities.edit', compact('mortality', 'birds'));
    }

    public function update(Request $request, Mortalities $mortality)
    {
        try {
            $validated = $request->validate([
                'bird_id' => 'required|exists:birds,id',
                'date' => 'required|date',
                'quantity' => 'required|integer|min:1',
                'cause' => 'nullable|string|max:255',
            ]);

            $mortality->update($validated);

            $bird = Bird::find($validated['bird_id']);
            if ($bird->stage === 'chick') {
                $totalDead = Mortalities::where('bird_id', $bird->id)
                    ->whereNull('deleted_at')
                    ->sum('quantity');
                $bird->dead = $totalDead;
                $bird->save();
            }

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'updated_mortality',
                'details' => "Updated mortality ID {$mortality->id} for bird ID {$validated['bird_id']}",
            ]);

            return redirect()->route('mortalities.index')->with('success', 'Mortality record updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update mortality', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to update mortality record.');
        }
    }

    public function destroy($id)
    {
        try {
            $mortality = Mortalities::whereNull('deleted_at')->findOrFail($id);
            $bird = Bird::find($mortality->bird_id);

            $mortality->delete();

            if ($bird && $bird->stage === 'chick') {
                $totalDead = Mortalities::where('bird_id', $bird->id)
                    ->whereNull('deleted_at')
                    ->sum('quantity');
                $bird->dead = $totalDead;
                $bird->save();
            }

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'deleted_mortality',
                'details' => "Deleted mortality ID {$id}",
            ]);

            return redirect()->route('mortalities.index')->with('success', 'Mortality record deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete mortality', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to delete mortality record.');
        }
    }
}