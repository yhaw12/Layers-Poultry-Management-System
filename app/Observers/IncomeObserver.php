<?php

namespace App\Observers;

use App\Models\Income;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class IncomeObserver
{
    public function created(Income $income)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'create',
                'model_type' => Income::class,
                'model_id'   => $income->id,
                'description'=> "Created income: " . ($income->description ?? 'ID ' . $income->id),
            ]);
        }
    }

    public function updated(Income $income)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'update',
                'model_type' => Income::class,
                'model_id'   => $income->id,
                'description'=> "Updated income: " . ($income->description ?? 'ID ' . $income->id),
            ]);
        }
    }

    public function deleted(Income $income)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'delete',
                'model_type' => Income::class,
                'model_id'   => $income->id,
                'description'=> "Deleted income: " . ($income->description ?? 'ID ' . $income->id),
            ]);
        }
    }

    public function restored(Income $income)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'restore',
                'model_type' => Income::class,
                'model_id'   => $income->id,
                'description'=> "Restored income: " . ($income->description ?? 'ID ' . $income->id),
            ]);
        }
    }

    public function forceDeleted(Income $income)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'force_delete',
                'model_type' => Income::class,
                'model_id'   => $income->id,
                'description'=> "Force deleted income: " . ($income->description ?? 'ID ' . $income->id),
            ]);
        }
    }
}
