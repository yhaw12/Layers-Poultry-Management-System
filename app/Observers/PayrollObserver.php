<?php

namespace App\Observers;

use App\Models\Payroll;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class PayrollObserver
{
    /**
     * Handle the Payroll "created" event.
     */
    public function created(Payroll $payroll)
    {
        if (Auth::check()) {
            $employeeName = $payroll->employee ? $payroll->employee->name : 'Unknown';
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'model_type' => Payroll::class,
                'model_id' => $payroll->id,
                'description' => "Created payroll for employee: {$employeeName}",
            ]);
        }
    }

    /**
     * Handle the Payroll "updated" event.
     */
    public function updated(Payroll $payroll)
    {
        if (Auth::check()) {
            $employeeName = $payroll->employee ? $payroll->employee->name : 'Unknown';
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'model_type' => Payroll::class,
                'model_id' => $payroll->id,
                'description' => "Updated payroll for employee: {$employeeName}",
            ]);
        }
    }

    /**
     * Handle the Payroll "deleted" event.
     */
    public function deleted(Payroll $payroll)
    {
        if (Auth::check()) {
            $employeeName = $payroll->employee ? $payroll->employee->name : 'Unknown';
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'model_type' => Payroll::class,
                'model_id' => $payroll->id,
                'description' => "Deleted payroll for employee: {$employeeName}",
            ]);
        }
    }

    /**
     * Handle the Payroll "restored" event.
     */
    public function restored(Payroll $payroll): void
    {
        if (Auth::check()) {
            $employeeName = $payroll->employee ? $payroll->employee->name : 'Unknown';
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'restore',
                'model_type' => Payroll::class,
                'model_id' => $payroll->id,
                'description' => "Restored payroll for employee: {$employeeName}",
            ]);
        }
    }

    /**
     * Handle the Payroll "force deleted" event.
     */
    public function forceDeleted(Payroll $payroll): void
    {
        if (Auth::check()) {
            $employeeName = $payroll->employee ? $payroll->employee->name : 'Unknown';
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'force_delete',
                'model_type' => Payroll::class,
                'model_id' => $payroll->id,
                'description' => "Force deleted payroll for employee: {$employeeName}",
            ]);
        }
    }
}