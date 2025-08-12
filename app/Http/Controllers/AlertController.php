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
                Log::warning('Unauthorized access attempt to alerts index');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $preferences = $user->notification_preferences ?? [
                'email' => true,
                'in_app' => true,
                'critical_only' => false,
            ];

            if (!$preferences['in_app']) {
                return response()->json([]);
            }

            $notifications = [];
            $isAdmin = $user->hasRole('admin');

            $start = $request->input('start_date', '2025-02-01');
            $end = $request->input('end_date', '2025-08-31');
            $period = [$start, $end];

            if ($isAdmin) {
                if (Schema::hasTable('alerts')) {
                    $alerts = Alert::where('is_read', false)
                        ->whereBetween('created_at', $period)
                        ->get();

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
                }
            } else {
                if (Schema::hasTable('alerts')) {
                    $alerts = Alert::where('user_id', $user->id)
                        ->where('is_read', false)
                        ->whereBetween('created_at', $period)
                        ->take(50)
                        ->get();

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
                }
            }

            return response()->json($notifications);
        } catch (\Exception $e) {
            Log::error('Failed to fetch notifications', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
 гигать 'error' => 'Failed to fetch notifications',
                'notifications' => [],
            ], 500);
        }
    }

    public function view(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                Log::warning('Unauthorized access attempt to alerts view');
                return view('alerts.index', ['alerts' => collect(), 'error' => 'Unauthorized']);
            }

            $start = $request->input('start_date', '2025-02-01');
            $end = $request->input('end_date', '2025-08-31');

            $alerts = Alert::where('user_id', $user->id)
                ->whereBetween('created_at', [$start, $end])
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('alerts.index', compact('alerts'));
        } catch (\Exception $e) {
            Log::error('Failed to load alerts view', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return view('alerts.index', ['alerts' => collect(), 'error' => 'Failed to load alerts']);
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