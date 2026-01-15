<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        // If the request is coming from the Browser Address Bar (Normal Page Load)
        // We should show the full notifications page.
        if (!$request->expectsJson() && !$request->ajax()) {
            return redirect()->route('notifications.index');
        }

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Logic to get unread alerts
            $query = Alert::where('is_read', false);
            if (!$user->hasRole('admin')) {
                $query->where('user_id', $user->id);
            }

            $alerts = $query->latest()->take(10)->get();

            // Convert to a simple array for the JS dropdown
            $notifications = $alerts->map(function ($alert) {
                return [
                    'id' => (string)$alert->id,
                    'message' => $alert->message,
                    'type' => $alert->type,
                    'url' => $alert->url ?? '#',
                    'time' => optional($alert->created_at)->diffForHumans() ?? 'Just now',
                ];
            });

            // CRITICAL: Ensure we return JSON here
            return response()->json($notifications);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error'], 500);
        }
    }

    public function view(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login');
            }

            $query = $user->hasRole('admin') 
                ? Alert::where('is_read', false) 
                : Alert::where('user_id', $user->id)->where('is_read', false);

            $alerts = $query->orderBy('created_at', 'desc')->paginate(10);

            return view('notifications.index', compact('alerts'));
        } catch (\Exception $e) {
            Log::error('Failed to load alerts view', ['error' => $e->getMessage()]);
            return view('notifications.index', ['alerts' => collect(), 'error' => 'Failed to load alerts']);
        }
    }

    public function read(Request $request, Alert $alert)
    {
        try {
            $user = Auth::user();
            
            // Allow admins to read any alert, or users to read their own
            if (!$user || ($alert->user_id != $user->id && !$user->hasRole('admin'))) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $alert->update(['is_read' => true, 'read_at' => now()]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error processing request'], 500);
        }
    }

    public function dismissAll(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            Alert::where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            // Return JSON success instead of a view for AJAX calls
            return response()->json(['success' => true, 'message' => 'All alerts dismissed']);
        } catch (\Exception $e) {
            Log::error('Dismiss all error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}