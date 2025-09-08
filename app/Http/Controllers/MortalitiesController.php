<?php

namespace App\Http\Controllers;

use App\Models\Mortalities;
use App\Models\Bird;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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

            $mortalities = Cache::remember($cacheKey, 3, function () use ($start, $end) {
                return Mortalities::with('bird')
                    ->whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->orderBy('date', 'desc')
                    ->paginate(10);
            });

            $totalMortalities = Cache::remember("total_mortalities_{$start}_{$end}", 3, function () use ($start, $end) {
                return Mortalities::whereBetween('date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('quantity') ?? 0;
            });

            return view('mortalities.index', compact('mortalities', 'totalMortalities', 'start', 'end'));
        } catch (\Exception $e) {
            Log::error('Failed to load mortalities', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load mortalities. Please try again.');
        }
    }

    public function create()
    {
        $birds = Bird::whereNull('deleted_at')->get();
        if ($birds->isEmpty()) {
            Log::warning('No birds found for mortality creation');
            return back()->with('error', 'No bird batches available. Please add a bird batch first.');
        }
        return view('mortalities.create', compact('birds'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'bird_id' => 'required|exists:birds,id',
                'date' => 'required|date|before_or_equal:today',
                'quantity' => 'required|integer|min:1',
                'cause' => 'nullable|string|max:255',
            ], [
                'bird_id.required' => 'Please select a bird batch.',
                'bird_id.exists' => 'The selected bird batch does not exist.',
                'date.required' => 'Please provide a date.',
                'date.before_or_equal' => 'The date cannot be in the future.',
                'quantity.required' => 'Please provide a quantity.',
                'quantity.min' => 'Quantity must be at least 1.',
                'cause.max' => 'Cause cannot exceed 255 characters.',
            ]);

            $bird = Bird::findOrFail($validated['bird_id']);
            Log::info('Creating mortality record', ['data' => $validated]);

            $mortality = Mortalities::create($validated);

            if ($bird->stage === 'chick') {
                $totalDead = Mortalities::where('bird_id', $bird->id)
                    ->whereNull('deleted_at')
                    ->sum('quantity');
                $bird->dead = $totalDead;
                $bird->save();
                Log::info('Updated bird dead count', ['bird_id' => $bird->id, 'dead' => $totalDead]);
            }

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'created_mortality',
                'details' => "Recorded {$validated['quantity']} mortalities for bird ID {$validated['bird_id']} on {$validated['date']}",
            ]);

            DB::commit();
            Log::info('Mortality record created successfully', ['mortality_id' => $mortality->id]);

            return redirect()->route('mortalities.index')->with('success', 'Mortality record added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed for mortality store', ['errors' => $e->errors(), 'input' => $request->all()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store mortality', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'input' => $request->all()]);
            return back()->with('error', 'Failed to add mortality record. Please try again.')->withInput();
        }
    }

    public function edit(Mortalities $mortality)
    {
        $birds = Bird::whereNull('deleted_at')->get();
        if ($birds->isEmpty()) {
            Log::warning('No birds found for mortality edit');
            return back()->with('error', 'No bird batches available. Please add a bird batch first.');
        }
        return view('mortalities.edit', compact('mortality', 'birds'));
    }

    public function update(Request $request, Mortalities $mortality)
    {
        try {
            $validated = $request->validate([
                'bird_id' => 'required|exists:birds,id',
                'date' => 'required|date|before_or_equal:today',
                'quantity' => 'required|integer|min:1',
                'cause' => 'nullable|string|max:255',
            ], [
                'bird_id.required' => 'Please select a bird batch.',
                'bird_id.exists' => 'The selected bird batch does not exist.',
                'date.required' => 'Please provide a date.',
                'date.before_or_equal' => 'The date cannot be in the future.',
                'quantity.required' => 'Please provide a quantity.',
                'quantity.min' => 'Quantity must be at least 1.',
                'cause.max' => 'Cause cannot exceed 255 characters.',
            ]);

            $mortality->update($validated);

            $bird = Bird::findOrFail($validated['bird_id']);
            if ($bird->stage === 'chick') {
                $totalDead = Mortalities::where('bird_id', $bird->id)
                    ->whereNull('deleted_at')
                    ->sum('quantity');
                $bird->dead = $totalDead;
                $bird->save();
                Log::info('Updated bird dead count', ['bird_id' => $bird->id, 'dead' => $totalDead]);
            }

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'updated_mortality',
                'details' => "Updated mortality ID {$mortality->id} for bird ID {$validated['bird_id']}",
            ]);

            Log::info('Mortality record updated successfully', ['mortality_id' => $mortality->id]);
            return redirect()->route('mortalities.index')->with('success', 'Mortality record updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for mortality update', ['errors' => $e->errors(), 'input' => $request->all()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update mortality', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'input' => $request->all()]);
            return back()->with('error', 'Failed to update mortality record. Please try again.')->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $mortality = Mortalities::whereNull('deleted_at')->findOrFail($id);
            $bird = Bird::find($mortality->bird_id);

            if ($bird && $bird->stage === 'chick') {
                $totalDead = Mortalities::where('bird_id', $bird->id)
                    ->whereNull('deleted_at')
                    ->sum('quantity');
                $bird->dead = $totalDead;
                $bird->save();
                Log::info('Updated bird dead count after deletion', ['bird_id' => $bird->id, 'dead' => $totalDead]);
            }

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'deleted_mortality',
                'details' => "Deleted mortality record of {$mortality->quantity} on {$mortality->date}" . ($mortality->cause ? " (Cause: {$mortality->cause})" : ""),
            ]);

            $mortality->delete();
            Log::info('Mortality record deleted successfully', ['mortality_id' => $id]);

            return redirect()->route('mortalities.index')->with('success', 'Mortality record deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete mortality record', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'mortality_id' => $id]);
            return redirect()->route('mortalities.index')->with('error', 'Failed to delete mortality record. Please try again.');
        }
    }
}