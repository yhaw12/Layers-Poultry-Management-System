<?php

namespace App\Observers;

use App\Models\Mortalities;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class MortalitiesObserver
{
    public function created(Mortalities $mortality)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'create',
                'model_type' => Mortalities::class,
                'model_id'   => $mortality->id,
                'description'=> "Created mortality record: " . ($mortality->cause ?? 'ID ' . $mortality->id),
            ]);
        }
    }

    public function updated(Mortalities $mortality)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'update',
                'model_type' => Mortalities::class,
                'model_id'   => $mortality->id,
                'description'=> "Updated mortality record: " . ($mortality->cause ?? 'ID ' . $mortality->id),
            ]);
        }
    }

    public function deleted(Mortalities $mortality)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'delete',
                'model_type' => Mortalities::class,
                'model_id'   => $mortality->id,
                'description'=> "Deleted mortality record: " . ($mortality->cause ?? 'ID ' . $mortality->id),
            ]);
        }
    }

    public function restored(Mortalities $mortality)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'restore',
                'model_type' => Mortalities::class,
                'model_id'   => $mortality->id,
                'description'=> "Restored mortality record: " . ($mortality->cause ?? 'ID ' . $mortality->id),
            ]);
        }
    }

    public function forceDeleted(Mortalities $mortality)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'force_delete',
                'model_type' => Mortalities::class,
                'model_id'   => $mortality->id,
                'description'=> "Force deleted mortality record: " . ($mortality->cause ?? 'ID ' . $mortality->id),
            ]);
        }
    }
}
