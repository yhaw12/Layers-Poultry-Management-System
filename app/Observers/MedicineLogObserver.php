<?php

namespace App\Observers;

use App\Models\MedicineLog;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class MedicineLogObserver
{
    /**
     * Handle the MedicineLog "created" event.
     */
    public function created(MedicineLog $medicineLog)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'model_type' => MedicineLog::class,
                'model_id' => $medicineLog->id,
                'description' => "Created medicine log: " . ($medicineLog->name ?? 'ID ' . $medicineLog->id),
            ]);
        }
    }

    /**
     * Handle the MedicineLog "updated" event.
     */
    public function updated(MedicineLog $medicineLog)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'model_type' => MedicineLog::class,
                'model_id' => $medicineLog->id,
                'description' => "Updated medicine log: " . ($medicineLog->name ?? 'ID ' . $medicineLog->id),
            ]);
        }
    }

    /**
     * Handle the MedicineLog "deleted" event.
     */
    public function deleted(MedicineLog $medicineLog)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'model_type' => MedicineLog::class,
                'model_id' => $medicineLog->id,
                'description' => "Deleted medicine log: " . ($medicineLog->name ?? 'ID ' . $medicineLog->id),
            ]);
        }
    }

    /**
     * Handle the MedicineLog "restored" event.
     */
    public function restored(MedicineLog $medicineLog): void
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'restore',
                'model_type' => MedicineLog::class,
                'model_id' => $medicineLog->id,
                'description' => "Restored medicine log: " . ($medicineLog->name ?? 'ID ' . $medicineLog->id),
            ]);
        }
    }

    /**
     * Handle the MedicineLog "force deleted" event.
     */
    public function forceDeleted(MedicineLog $medicineLog): void
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'force_delete',
                'model_type' => MedicineLog::class,
                'model_id' => $medicineLog->id,
                'description' => "Force deleted medicine log: " . ($medicineLog->name ?? 'ID ' . $medicineLog->id),
            ]);
        }
    }
}