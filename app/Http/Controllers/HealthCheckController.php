<?php

namespace App\Http\Controllers;

use App\Models\Bird;
use App\Models\HealthCheck;
use Illuminate\Http\Request;

class HealthCheckController extends Controller
{
    public function index()
    {
        $healthChecks = HealthCheck::with('bird')->orderBy('date', 'desc')->paginate(10);
        return view('health-checks.index', compact('healthChecks'));
    }

    public function create()
    {
        $birds = Bird::all();
        return view('health-checks.create', compact('birds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bird_id' => 'required|exists:birds,id',
            'date' => 'required|date',
            'status' => 'required|string|max:255',
            'symptoms' => 'nullable|string',
            'treatment' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        HealthCheck::create($validated);
        return redirect()->route('health-checks.index')->with('success', 'Health check logged successfully.');
    }
}
