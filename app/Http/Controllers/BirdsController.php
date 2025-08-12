<?php

namespace App\Http\Controllers;

use App\Models\Bird;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BirdsController extends Controller
{
    public function index()
    {
        $birds = Bird::paginate(10);
        $totalQuantity = Bird::whereNull('deleted_at')->sum('quantity') ?? 0;
        $chicks = Bird::where('stage', 'chick')
            ->whereNull('deleted_at')
            ->sum('alive') ?? 0;
        $layers = Bird::where('type', 'layer')
            ->whereNull('deleted_at')
            ->sum(Db::raw('CASE WHEN stage = "chick" THEN alive ELSE quantity END')) ?? 0;
        $broilers = Bird::where('type', 'broiler')
            ->whereNull('deleted_at')
            ->sum(DB::raw('CASE WHEN stage = "chick" THEN alive ELSE quantity END')) ?? 0;
        return view('birds.index', compact('birds', 'totalQuantity', 'layers', 'broilers', 'chicks'));
    }

    public function create()
    {
        return view('birds.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'breed' => 'required|string|max:255',
            'type' => 'required|in:layer,broiler',
            'quantity' => 'required|integer|min:1',
            'working' => 'required|boolean',
            'entry_date' => 'required|date',
            'vaccination_status' => 'nullable|boolean',
            'housing_location' => 'nullable|string|max:255',
            'stage' => 'required|in:chick,juvenile,adult',
        ];

        if ($request->stage === 'chick') {
            $rules = array_merge($rules, [
                'quantity_bought' => 'required|integer|min:1',
                'feed_amount' => 'required|numeric|min:0',
                'alive' => 'required|integer|min:0',
                'dead' => 'required|integer|min:0',
                'purchase_date' => 'required|date',
                'cost' => 'required|numeric|min:0',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        // Validate alive + dead <= quantity_bought for chicks
        if ($request->stage === 'chick') {
            $validator->after(function ($validator) use ($request) {
                $alive = (int) $request->input('alive');
                $dead = (int) $request->input('dead');
                $quantityBought = (int) $request->input('quantity_bought');
                if ($alive + $dead > $quantityBought) {
                    $validator->errors()->add('alive', 'The sum of alive and dead birds cannot exceed quantity bought.');
                }
            });
        }

        $validated = $validator->validate();

        // Calculate age based on entry_date or purchase_date
        $referenceDate = $request->stage === 'chick' ? $validated['purchase_date'] : $validated['entry_date'];
        $validated['age'] = Carbon::parse($referenceDate)->diffInWeeks(Carbon::now());

        $bird = Bird::create($validated);

        // If dead > 0 for chicks, create a Mortalities record
        if ($request->stage === 'chick' && $validated['dead'] > 0) {
            \App\Models\Mortalities::create([
                'bird_id' => $bird->id,
                'date' => $validated['purchase_date'],
                'quantity' => $validated['dead'],
                'cause' => 'Initial mortality on purchase',
            ]);
        }

        return redirect()->route('birds.index')->with('success', 'Bird batch added successfully.');
    }

    public function edit(Bird $bird)
    {
        return view('birds.edit', compact('bird'));
    }

    public function update(Request $request, Bird $bird)
    {
        $rules = [
            'breed' => 'required|string|max:255',
            'type' => 'required|in:layer,broiler',
            'quantity' => 'required|integer|min:1',
            'working' => 'required|boolean',
            'entry_date' => 'required|date',
            'vaccination_status' => 'nullable|boolean',
            'housing_location' => 'nullable|string|max:255',
            'stage' => 'required|in:chick,juvenile,adult',
        ];

        if ($request->stage === 'chick') {
            $rules = array_merge($rules, [
                'quantity_bought' => 'required|integer|min:1',
                'feed_amount' => 'required|numeric|min:0',
                'alive' => 'required|integer|min:0',
                'dead' => 'required|integer|min:0',
                'purchase_date' => 'required|date',
                'cost' => 'required|numeric|min:0',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        // Validate alive + dead <= quantity_bought for chicks
        if ($request->stage === 'chick') {
            $validator->after(function ($validator) use ($request) {
                $alive = (int) $request->input('alive');
                $dead = (int) $request->input('dead');
                $quantityBought = (int) $request->input('quantity_bought');
                if ($alive + $dead > $quantityBought) {
                    $validator->errors()->add('alive', 'The sum of alive and dead birds cannot exceed quantity bought.');
                }
            });
        }

        $validated = $validator->validate();

        // Calculate age based on entry_date or purchase_date
        $referenceDate = $request->stage === 'chick' ? $validated['purchase_date'] : $validated['entry_date'];
        $validated['age'] = Carbon::parse($referenceDate)->diffInWeeks(Carbon::now());

        // Update Mortalities if dead field changed for chicks
        if ($request->stage === 'chick' && $validated['dead'] != $bird->dead) {
            $mortality = \App\Models\Mortalities::where('bird_id', $bird->id)
                ->where('date', $validated['purchase_date'])
                ->first();

            if ($mortality) {
                $mortality->update([
                    'quantity' => $validated['dead'],
                    'cause' => $mortality->cause ?? 'Updated mortality',
                ]);
            } else {
                \App\Models\Mortalities::create([
                    'bird_id' => $bird->id,
                    'date' => $validated['purchase_date'],
                    'quantity' => $validated['dead'],
                    'cause' => 'Updated mortality',
                ]);
            }
        }

        $bird->update($validated);

        return redirect()->route('birds.index')->with('success', 'Bird batch updated successfully.');
    }

    public function destroy($id)
    {
        $bird = Bird::findOrFail($id);
        $bird->delete();
        return redirect()->route('birds.index')->with('success', 'Bird batch deleted successfully.');
    }

    public function trashed()
    {
        $birds = Bird::onlyTrashed()->get();
        return view('birds.trashed', compact('birds'));
    }

    public function restore($id)
    {
        $bird = Bird::withTrashed()->findOrFail($id);
        $bird->restore();
        return redirect()->route('birds.index')->with('success', 'Bird restored successfully.');
    }
}