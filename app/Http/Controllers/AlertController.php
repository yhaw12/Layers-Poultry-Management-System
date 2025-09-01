<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AlertController extends Controller
{
    public function index(Request $request)
{
    try {
        $user = Auth::user();
        if (!$user) {
            Log::warning('Unauthorized access attempt to alerts index', ['request' => $request->all()]);
            return response()->json(['error' => 'Unauthorized'], 401)->header('Content-Type', 'application/json');
        }

        $preferences = $user->notification_preferences ?? [
            'email' => true,
            'in_app' => true,
            'critical_only' => false,
        ];

        if (!$preferences['in_app']) {
            return response()->json([])->header('Content-Type', 'application/json');
        }

        $notifications = [];
        $isAdmin = $user->hasRole('admin');

        if ($isAdmin) {
            $alerts = Alert::where('is_read', false)->get();
        } else {
            $alerts = Alert::where('user_id', $user->id)->where('is_read', false)->take(50)->get();
        }

        foreach ($alerts as $alert) {
            if (!$preferences['critical_only'] || $alert->type === 'critical') {
                $notifications[] = [
                    'id' => $alert->id,
                    'message' => $alert->message,
                    'type' => $alert->type,
                    'url' => $alert->url ?? '#',
                    'created_at' => $alert->created_at->toDateTimeString(),
                ];
            }
        }

        return response()->json($notifications)->header('Content-Type', 'application/json');
    } catch (\Exception $e) {
        Log::error('Failed to fetch notifications', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => $user->id ?? 'none',
        ]);
        return response()->json(['error' => 'Failed to fetch notifications', 'notifications' => []], 500)
            ->header('Content-Type', 'application/json');
    }
}

    public function view(Request $request)
{
    try {
        $user = Auth::user();
        if (!$user) {
            Log::warning('Unauthorized access attempt to alerts view');
            return view('notifications.index', ['alerts' => collect(), 'error' => 'Unauthorized']);
        }

        $query = $user->hasRole('admin') 
            ? Alert::where('is_read', false) // Admins see all unread alerts
            : Alert::where('user_id', $user->id)->where('is_read', false);

        $alerts = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('notifications.index', compact('alerts'));
    } catch (\Exception $e) {
        Log::error('Failed to load alerts view', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return view('notifications.index', ['alerts' => collect(), 'error' => 'Failed to load alerts']);
    }
}
    public function read(Request $request, Alert $alert)
    {
        try {
            $user = Auth::user();
            if (!$user || $alert->user_id != $user->id) {
                Log::warning('Unauthorized attempt to mark alert as read', ['user_id' => $user->id ?? null, 'alert_id' => $alert->id]);
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $alert->update(['is_read' => true, 'read_at' => now()]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to mark alert as read', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to mark alert as read'], 500);
        }
    }

    public function dismissAll(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                Log::warning('Unauthorized attempt to dismiss all alerts');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            Alert::where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to dismiss all alerts', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to dismiss all alerts'], 500);
        }
    }
}