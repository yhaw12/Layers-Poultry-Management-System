<?php

namespace App\Http\Controllers;

use App\Models\Reminder;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = Reminder::where('is_done',false)
            ->orderBy('severity','desc')
            ->latest()
            ->get();

        return view('reminders.index', compact('reminders'));
    }
}
