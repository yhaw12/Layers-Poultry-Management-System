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
        try {
            $user = Auth::user();
            if (!$user) {
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

            // Define period for consistency with DashboardController
            $start = $request->input('start_date', now()->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());
            $period = [$start, $end];

            if ($isAdmin) {
                // Fetch all unread alerts for admins
                if (Schema::hasTable('alerts')) {
                    $alerts = Alert::where('is_read', false)
                        ->whereBetween('created_at', $period)
                        ->get();

                    foreach ($alerts as $alert) {
                        if (!$preferences['critical_only'] || $alert->type === 'critical') {
                            $notifications[] = [
                                'id' => $alert->id ?? (string) Str::uuid(),
                                'message' => $alert->message,
                                'type' => $alert->type,
                                'url' => $alert->url ?? '#',
                                'created_at' => $alert->created_at ? $alert->created_at->toDateTimeString() : now()->toDateTimeString(),
                            ];
                        }
                    }

                    // Add low stock alerts from cache
                    $lowStockAlerts = Cache::remember('low_stock_alerts', 3600, function () use ($period) {
                        $lowStockAlerts = collect();

                        // Low inventory alerts
                        $lowInventory = Inventory::where('qty', '<', DB::raw('threshold'))
                            ->whereBetween('updated_at', $period)
                            ->get()
                            ->map(function ($item) {
                                $alert = new Alert([
                                    'id' => (string) Str::uuid(),
                                    'message' => "Low stock for " . ($item->name ?? 'Unknown Item') . ": {$item->qty} remaining (Threshold: {$item->threshold})",
                                    'type' => 'warning',
                                    'is_read' => false,
                                    'created_at' => now(),
                                    'user_id' => null,
                                ]);
                                return $alert;
                            });
                        $lowStockAlerts = $lowStockAlerts->concat($lowInventory);

                        // Low feed alerts
                        $lowFeed = Feed::where('quantity', '<', DB::raw('threshold'))
                            ->whereBetween('purchase_date', $period)
                            ->get()
                            ->map(function ($item) {
                                $alert = new Alert([
                                    'id' => (string) Str::uuid(),
                                    'message' => "Low feed stock for " . ($item->name ?? 'Unknown Feed') . ": {$item->quantity} kg remaining (Threshold: {$item->threshold} kg)",
                                    'type' => 'warning',
                                    'is_read' => false,
                                    'created_at' => now(),
                                    'user_id' => null,
                                ]);
                                return $alert;
                            });
                        $lowStockAlerts = $lowStockAlerts->concat($lowFeed);

                        // Low medicine alerts (skip for now due to missing MedicineLog model)
                        /*
                        $lowMedicine = MedicineLog::select('medicine_name')
                            ->selectRaw('SUM(CASE WHEN type = "purchase" THEN quantity ELSE -quantity END) as net_quantity')
                            ->whereBetween('date', $period)
                            ->groupBy('medicine_name')
                            ->havingRaw('net_quantity < ?', [10])
                            ->get()
                            ->map(function ($item) {
                                $alert = new Alert([
                                    'id' => (string) Str::uuid(),
                                    'message' => "Low medicine stock for " . ($item->medicine_name ?? 'Unknown Medicine') . ": {$item->net_quantity} units remaining (Threshold: 10 units)",
                                    'type' => 'warning',
                                    'is_read' => false,
                                    'created_at' => now(),
                                    'user_id' => null,
                                ]);
                                return $alert;
                            });
                        $lowStockAlerts = $lowStockAlerts->concat($lowMedicine);
                        */

                        return $lowStockAlerts;
                    });

                    foreach ($lowStockAlerts as $alert) {
                        if (!$preferences['critical_only'] || $alert->type === 'critical') {
                            $notifications[] = [
                                'id' => $alert->id ?? (string) Str::uuid(),
                                'message' => $alert->message,
                                'type' => $alert->type,
                                'url' => $alert->url ?? '#',
                                'created_at' => $alert->created_at ? $alert->created_at->toDateTimeString() : now()->toDateTimeString(),
                            ];
                        }
                    }
                }
            } else {
                // Fetch unread alerts for non-admin users
                if ($user->hasRole('inventory_manager')) {
                    if (Feed::where('quantity', '<', 100)->exists()) {
                        $alert = Alert::firstOrCreate(
                            [
                                'user_id' => $user->id,
                                'message' => 'Feed stock is low (< 100 kg). Restock soon.',
                            ],
                            [
                                'id' => (string) Str::uuid(),
                                'type' => 'critical',
                                'is_read' => false,
                                'url' => route('feed.index'),
                                'created_at' => now(),
                            ]
                        );
                        $notifications[] = [
                            'id' => $alert->id ?? (string) Str::uuid(),
                            'message' => $alert->message,
                            'type' => $alert->type,
                            'url' => $alert->url ?? '#',
                            'created_at' => $alert->created_at ? $alert->created_at->toDateTimeString() : now()->toDateTimeString(),
                        ];
                    }

                    $newInventory = Inventory::where('created_at', '>=', now()->subDay())->get();
                    foreach ($newInventory as $item) {
                        if (!$preferences['critical_only'] || $item->quantity > 1000) {
                            $alert = Alert::firstOrCreate(
                                [
                                    'user_id' => $user->id,
                                    'message' => "New inventory item added: " . ($item->name ?? 'Unknown Item') . " ({$item->quantity} units).",
                                ],
                                [
                                    'id' => (string) Str::uuid(),
                                    'type' => $item->quantity > 1000 ? 'critical' : 'info',
                                    'is_read' => false,
                                    'url' => route('inventory.index'),
                                    'created_at' => now(),
                                ]
                            );
                            $notifications[] = [
                                'id' => $alert->id ?? (string) Str::uuid(),
                                'message' => $alert->message,
                                'type' => $alert->type,
                                'url' => $alert->url ?? '#',
                                'created_at' => $alert->created_at ? $alert->created_at->toDateTimeString() : now()->toDateTimeString(),
                            ];
                        }
                    }
                }

                // Fetch existing unread alerts for the user
                if (Schema::hasTable('alerts')) {
                    $alerts = Alert::where('user_id', $user->id)
                        ->where('is_read', false)
                        ->whereBetween('created_at', $period)
                        ->take(50)
                        ->get();

                    foreach ($alerts as $alert) {
                        if (!$preferences['critical_only'] || $alert->type === 'critical') {
                            $notifications[] = [
                                'id' => $alert->id ?? (string) Str::uuid(),
                                'message' => $alert->message,
                                'type' => $alert->type,
                                'url' => $alert->url ?? '#',
                                'created_at' => $alert->created_at ? $alert->created_at->toDateTimeString() : now()->toDateTimeString(),
                            ];
                        }
                    }
                }
            }

            return response()->json($notifications);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch notifications', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Failed to fetch notifications',
                'notifications' => [],
            ], 500);
        }
    }

    /**
     * Display a paginated list of alerts for the alerts.index view.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function view(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $isAdmin = $user->hasRole('admin');
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());
        $period = [$start, $end];

        $alerts = collect();
        if ($isAdmin) {
            $alerts = Alert::where('is_read', false)
                ->whereBetween('created_at', $period)
                ->paginate(10);
        } else {
            $alerts = Alert::where('user_id', $user->id)
                ->where('is_read', false)
                ->whereBetween('created_at', $period)
                ->paginate(10);
        }

        return view('alerts.index', compact('alerts'));
    }

    /**
     * Mark a specific alert as read.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            if (!Schema::hasTable('alerts') || !Schema::hasColumn('alerts', 'is_read')) {
                return response()->json(['success' => false, 'error' => 'Alerts table or is_read column missing'], 500);
            }

            $alert = Alert::where('id', $id)
                ->when(!Auth::user()->hasRole('admin'), function ($query) {
                    return $query->where('user_id', Auth::id());
                })
                ->first();

            if ($alert) {
                $alert->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

                UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'marked_notification_as_read',
                    'details' => json_encode(['notification_id' => $id, 'message' => $alert->message]),
                ]);

                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'error' => 'Alert not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Failed to mark alert as read', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'error' => 'Failed to mark alert as read'], 500);
        }
    }

    /**
     * Dismiss all unread alerts for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dismissAll(Request $request)
    {
        try {
            if (!Schema::hasTable('alerts') || !Schema::hasColumn('alerts', 'is_read')) {
                return response()->json(['success' => false, 'error' => 'Alerts table or is_read column missing'], 500);
            }

            $query = Alert::where('is_read', false);
            if (!Auth::user()->hasRole('admin')) {
                $query->where('user_id', Auth::id());
            }

            $updated = $query->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

            if ($updated) {
                UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'dismissed_all_notifications',
                    'details' => json_encode(['count' => $updated]),
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Failed to dismiss all alerts', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'error' => 'Failed to dismiss all alerts'], 500);
        }
    }
}