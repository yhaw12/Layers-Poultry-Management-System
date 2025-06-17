<?php

namespace App\Http\Controllers;

use App\Models\Mortalities;
use App\Models\Bird;
use Illuminate\Http\Request;

class MortalitiesController extends Controller
{
    public function index()
    {
        $mortalities = Mortalities::with('bird')->orderBy('date', 'desc')->paginate(10);
        $totalMortalities = Mortalities::sum('quantity') ?? 0;
        return view('mortalities.index', compact('mortalities', 'totalMortalities'));
    }

    public function create()
    {
        $birds = Bird::all();
        return view('mortalities.create', compact('birds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bird_id' => 'required|exists:birds,id',
            'date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'cause' => 'nullable|string|max:255',
        ]);

        Mortalities::create($validated);
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id,
            'action' => 'created_mortality',
            'description' => "Recorded {$validated['quantity']} mortalities for bird ID {$validated['bird_id']} on {$validated['date']}",
        ]);

        return redirect()->route('mortalities.index')->with('success', 'Mortality record added successfully.');
    }

    public function edit(Mortalities $mortality)
    {
        $birds = Bird::all();
        return view('mortalities.edit', compact('mortality', 'birds'));
    }

    public function update(Request $request, Mortalities $mortality)
    {
        $validated = $request->validate([
            'bird_id' => 'required|exists:birds,id',
            'date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'cause' => 'nullable|string|max:255',
        ]);

        $mortality->update($validated);
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id,
            'action' => 'updated_mortality',
            'description' => "Updated mortality ID {$mortality->id} for bird ID {$validated['bird_id']}",
        ]);

        return redirect()->route('mortalities.index')->with('success', 'Mortality record updated successfully.');
    }

    public function destroy($id)
    {
        $mortality = Mortalities::findorFail($id);
        $mortality->delete();
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id,
            'action' => 'deleted_mortality',
            'description' => "Deleted mortality ID {$mortality->id}",
        ]);

        return redirect()->route('mortalities.index')->with('success', 'Mortality record deleted successfully.');
    }
}