<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $preferences = $user->preferences ?? [
            'notifications' => ['email' => true],
            'theme' => 'system',
        ];
        return view('settings.index', compact('preferences'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'notifications.email' => ['boolean'],
            'theme' => ['in:light,dark,system'],
        ]);

        $user->update([
            'preferences' => [
                'notifications' => ['email' => $data['notifications']['email']],
                'theme' => $data['theme'],
            ],
        ]);

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }
}
