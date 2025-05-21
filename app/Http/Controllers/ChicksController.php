<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chicks;

class ChicksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chicks = Chicks::all();
        return view('chicks.index', compact('chicks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('chicks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'breed' => 'required|string|max:255',
            'quantity_bought' => 'required|integer|min:1',
            'feed_amount' => 'nullable|numeric|min:0',
            'alive' => 'required|integer|min:0',
            'dead' => 'required|integer|min:0',
            'purchase_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
        ]);
        Chicks::create($data);
        return redirect()->route('chicks.index')->with('success', 'Chicks added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('chicks.edit', compact('chick'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chicks $chick)
    {
        $data = $request->validate([
            'breed' => 'required|string|max:255',
            'quantity_bought' => 'required|integer|min:1',
            'feed_amount' => 'nullable|numeric|min:0',
            'alive' => 'required|integer|min:0',
            'dead' => 'required|integer|min:0',
            'purchase_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
        ]);

        $chick->update($data);
        return redirect()->route('chicks.index')->with('success', 'Chick record updated successfully');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chicks $chick)
    {
        $chick->delete();
        return redirect()->route('chicks.index')->with('success', 'Chick record deleted successfully');
    }
}
