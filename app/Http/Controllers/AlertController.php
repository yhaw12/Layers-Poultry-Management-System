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
        try {
            $user = Auth::user();
            
            if (!$user) {
                // Return JSON immediately for API calls
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $preferences = $user->notification_preferences ?? [
                'email' => true,
                'in_app' => true,
                'critical_only' => false,
            ];

            if (isset($preferences['in_app']) && !$preferences['in_app']) {
                return response()->json([]);
            }

            // Optimize query: Select only needed columns if possible
            $query = Alert::query();

            if ($user->hasRole('admin')) {
                $query->where('is_read', false);
            } else {
                $query->where('user_id', $user->id)->where('is_read', false);
            }

            // Limit results to prevent massive JSON payloads
            $alerts = $query->latest()->take(50)->get();

            $notifications = $alerts->map(function ($alert) use ($preferences) {
                // Skip if preference is critical only and alert is not critical
                if ($preferences['critical_only'] && $alert->type !== 'critical') {
                    return null;
                }

                return [
                    'id' => $alert->id,
                    'message' => $alert->message,
                    'type' => $alert->type,
                    'url' => $alert->url ?? '#',
                    // FIX: optional() allows toDateTimeString() to fail silently if created_at is null
                    'created_at' => optional($alert->created_at)->toDateTimeString(), 
                ];
            })->filter()->values(); // Remove nulls from map and re-index

            return response()->json($notifications);

        } catch (\Exception $e) {
            Log::error('Failed to fetch notifications', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id() ?? 'none',
            ]);
            
            return response()->json(['error' => 'Server error'], 500);
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