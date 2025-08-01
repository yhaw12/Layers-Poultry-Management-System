<?php

namespace App\Http\Controllers;

use App\Models\VaccinationLog;
use App\Models\Bird;
use Illuminate\Http\Request;

class VaccinationLogController extends Controller
{
    public function index()
    {
        $logs = VaccinationLog::with('bird', 'chicks')->orderBy('date_administered', 'desc')->paginate(10);
        return view('vaccination-logs.index', compact('logs'));
    }

    public function create()
    {
        $birds = Bird::all();
        return view('vaccination-logs.create', compact('birds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bird_id' => 'required|exists:birds,id',
            'vaccine_name' => 'required|string|max:255',
            'date_administered' => 'required|date',
            'notes' => 'nullable|string',
            'next_vaccination_date' => 'nullable|date|after:date_administered',
        ]);

        VaccinationLog::create($validated);
        return redirect()->route('vaccination-logs.index')->with('success', 'Vaccination log added.');
    }

    // Add edit, update, destroy methods similarly
     public function destroy($id)
    {
        $vaccine = VaccinationLog::findorFail($id);
        $vaccine->delete();
        return redirect()->route('vaccination-logs.index')->with('success', 'vaccines deleted successfully');
    }

    public function reminders()
    {
        $reminders = VaccinationLog::where('next_vaccination_date', '<=', now()->addDays(7))
            ->where('next_vaccination_date', '>=', now())
            ->with('bird')
            ->get();
        return view('vaccination-logs.reminders', compact('reminders'));
    }
}