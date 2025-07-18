<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use Illuminate\Http\Request;

class DiseaseController extends Controller
{
    public function index()
    {
        $diseases = Disease::orderBy('start_date', 'desc')->paginate(10);
        return view('diseases.index', compact('diseases'));
    }

    public function history(Disease $disease)
    {
        return view('diseases.history', compact('disease'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'treatments' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        Disease::create($validated);
        return redirect()->route('diseases.index')->with('success', 'Disease logged successfully.');
    }
}