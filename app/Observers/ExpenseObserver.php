<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'model_type' => Expense::class,
                'model_id' => $expense->id,
                'description' => "Created expense: " . ($expense->description ?? 'ID ' . $expense->id),
            ]);
        }
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'model_type' => Expense::class,
                'model_id' => $expense->id,
                'description' => "Updated expense: " . ($expense->description ?? 'ID ' . $expense->id),
            ]);
        }
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'model_type' => Expense::class,
                'model_id' => $expense->id,
                'description' => "Deleted expense: " . ($expense->description ?? 'ID ' . $expense->id),
            ]);
        }
    }

    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'restore',
                'model_type' => Expense::class,
                'model_id' => $expense->id,
                'description' => "Restored expense: " . ($expense->description ?? 'ID ' . $expense->id),
            ]);
        }
    }

    /**
     * Handle the Expense "force deleted" event.
     */
    public function forceDeleted(Expense $expense): void
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'force_delete',
                'model_type' => Expense::class,
                'model_id' => $expense->id,
                'description' => "Force deleted expense: " . ($expense->description ?? 'ID ' . $expense->id),
            ]);
        }
    }
}