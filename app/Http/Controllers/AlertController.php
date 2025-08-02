<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Feed;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AlertController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = [];

        try {
            $preferences = $user->notification_preferences ?? [
                'email' => true,
                'in_app' => true,
                'critical_only' => false,
            ];

            if (!$preferences['in_app']) {
                return response()->json([]);
            }

            // Check if user is admin
            if ($user->hasRole('admin')) {
                // Fetch all alerts for admins, regardless of user_id
                if (Schema::hasTable('alerts')) {
                    $alerts = Alert::where('is_read', false)->get();
                    foreach ($alerts as $alert) {
                        if (!$preferences['critical_only'] || $alert->type === 'critical') {
                            $notifications[] = [
                                'id' => $alert->id,
                                'message' => $alert->message,
                                'type' => $alert->type,
                                'url' => $alert->url ?? '#',
                            ];
                        }
                    }
                }
            } else {
                // Existing logic for non-admin users
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
                            ]
                        );
                        $notifications[] = [
                            'id' => $alert->id,
                            'message' => $alert->message,
                            'type' => $alert->type,
                            'url' => $alert->url ?? '#',
                        ];
                    }

                    $newInventory = Inventory::where('created_at', '>=', now()->subDay())->get();
                    foreach ($newInventory as $item) {
                        if (!$preferences['critical_only'] || $item->quantity > 1000) {
                            $alert = Alert::firstOrCreate(
                                [
                                    'user_id' => $user->id,
                                    'message' => "New inventory item added: {$item->name} ({$item->quantity} units).",
                                ],
                                [
                                    'id' => (string) Str::uuid(),
                                    'type' => $item->quantity > 1000 ? 'critical' : 'info',
                                    'is_read' => false,
                                    'url' => route('inventory.index'),
                                ]
                            );
                            $notifications[] = [
                                'id' => $alert->id,
                                'message' => $alert->message,
                                'type' => $alert->type,
                                'url' => $alert->url ?? '#',
                            ];
                        }
                    }
                }

                // Fetch existing unread alerts for the user
                if (Schema::hasTable('alerts')) {
                    $alerts = Alert::where('user_id', $user->id)
                        ->where('is_read', false)
                        ->get();
                    foreach ($alerts as $alert) {
                        if (!$preferences['critical_only'] || $alert->type === 'critical') {
                            $notifications[] = [
                                'id' => $alert->id,
                                'message' => $alert->message,
                                'type' => $alert->type,
                                'url' => $alert->url ?? '#',
                            ];
                        }
                    }
                }
            }

            return response()->json($notifications);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch notifications',
                'notifications' => $notifications,
            ], 500);
        }
    }

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
                \App\Models\UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Marked notification as read',
                    'details' => json_encode(['notification_id' => $id, 'message' => $alert->message]),
                ]);
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false, 'error' => 'Alert not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to mark alert as read'], 500);
        }
    }

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
                \App\Models\UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Dismissed all notifications',
                    'details' => json_encode(['count' => $updated]),
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            
            return response()->json(['success' => false, 'error' => 'Failed to dismiss all alerts'], 500);
        }
    }
}