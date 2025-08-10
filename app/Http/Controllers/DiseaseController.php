<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\HealthCheck;
use Illuminate\Http\Request;

class DiseaseController extends Controller
{
    public function index()
    {
        $diseases = Disease::orderBy('name')->paginate(10);
        return view('diseases.index', compact('diseases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:diseases,name',
        ]);

        Disease::create($validated);
        return redirect()->route('diseases.index')->with('success', 'Disease added successfully.');
    }

    public function history(Disease $disease)
    {
        $history = HealthCheck::where('disease_id', $disease->id)
            ->with('bird')
            ->orderBy('date', 'desc')
            ->paginate(10);
        return view('diseases.history', compact('disease', 'history'));
    }
}