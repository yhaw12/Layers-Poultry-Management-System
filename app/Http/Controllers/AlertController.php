<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Feed;
use App\Models\Inventory;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AlertController extends Controller
{
    /**
     * Display a listing of notifications for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Log the request for debugging
        Log::info('AlertController::index called', ['user_id' => Auth::id() ?? 'guest']);

        try {
            $user = Auth::user();
            if (!$user) {
                Log::warning('Unauthorized access to alerts', ['user_id' => Auth::id() ?? 'guest']);
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Default notification preferences (customize as needed)
            $preferences = $user->notification_preferences ?? [
                'email' => true,
                'in_app' => true,
                'critical_only' => false,
            ];
            Log::info('User notification preferences', ['preferences' => $preferences]);

            // If in-app notifications are disabled, return empty array
            if (!$preferences['in_app']) {
                Log::info('In-app notifications disabled for user', ['user_id' => $user->id]);
                return response()->json([]);
            }

            $notifications = [];
            $isAdmin = $user->hasRole('admin'); // Assumes a role system (e.g., Spatie Permission)

            // Date range for alerts
            $start = $request->input('start_date', now()->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());
            $period = [$start, $end];

            if ($isAdmin) {
                if (Schema::hasTable('alerts')) {
                    $alerts = Alert::where('is_read', false)
                        ->whereBetween('created_at', $period)
                        ->get();
                    Log::info('Admin alerts fetched', ['count' => $alerts->count()]);

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
                    Log::info('User-specific alerts fetched', ['count' => $alerts->count()]);

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

            Log::info('Notifications generated', ['notifications' => $notifications]);
            return response()->json($notifications);
        } catch (\Exception $e) {
            Log::error('Failed to fetch notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to fetch notifications',
                'notifications' => [],
            ], 500);
        }
    }

    public function view(Request $request)
{
    $user = Auth::user();
    if (!$user) {
        abort(401, 'Unauthorized');
    }

    $start = $request->input('start_date', now()->startOfMonth()->toDateString());
    $end = $request->input('end_date', now()->endOfMonth()->toDateString());

    $alerts = Alert::where('user_id', $user->id)
        ->whereBetween('created_at', [$start, $end])
        ->where('is_read', false)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('alerts.index', compact('alerts'));
}
}