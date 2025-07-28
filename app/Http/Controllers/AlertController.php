<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Feed;
use App\Models\Vaccination;
use App\Models\Expense;
use App\Models\Instruction;
use App\Models\VaccinationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = [];

        // Role-based notification checks
        if ($user->hasRole('inventory_manager')) {
            if (Feed::where('quantity', '<', 100)->exists()) {
                $notifications[] = [
                    'id' => 'feed-' . time(),
                    'message' => 'Feed stock is low (< 100 kg). Restock soon.',
                    'type' => 'critical'
                ];
            }
        }

        if ($user->hasRole('veterinarian')) {
            $overdueVaccinations = VaccinationLog::where('due_date', '<', now())->where('status', '!=', 'completed')->get();
            foreach ($overdueVaccinations as $vaccination) {
                $notifications[] = [
                    'id' => 'vaccination-' . $vaccination->id,
                    'message' => "Vaccination {$vaccination->vaccine_name} is overdue.",
                    'type' => 'critical'
                ];
            }
        }

        if ($user->hasRole('admin')) {
            $highExpenses = Expense::whereMonth('date', now()->month)->sum('amount') > 10000;
            if ($highExpenses) {
                $notifications[] = [
                    'id' => 'expense-' . time(),
                    'message' => 'Monthly expenses exceed $10,000.',
                    'type' => 'warning'
                ];
            }
        }

        if ($user->hasRole('labourer')) {
            $newInstructions = Instruction::where('created_at', '>=', now()->subDay())->count();
            if ($newInstructions > 0) {
                $notifications[] = [
                    'id' => 'instruction-' . time(),
                    'message' => "New daily instructions available ($newInstructions).",
                    'type' => 'info'
                ];
            }
        }

        // Fetch stored alerts
        $alerts = Alert::where('user_id', $user->id)->where('is_read', false)->get();
        foreach ($alerts as $alert) {
            $notifications[] = [
                'id' => $alert->id,
                'message' => $alert->message,
                'type' => $alert->type
            ];
        }

        return response()->json($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $alert = Alert::where('id', $id)->where('user_id', Auth::id())->first();
        if ($alert) {
            $alert->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function dismissAll(Request $request)
    {
        Alert::where('user_id', Auth::id())->where('is_read', false)->update(['is_read' => true]);
        return redirect()->back()->with('success', 'All alerts dismissed.');
    }
}
