<?php

namespace App\Observers;

use App\Models\Egg;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class EggObserver
{
    public function created(Egg $egg)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'create',
                'model_type' => Egg::class,
                'model_id'   => $egg->id,
                'description'=> "Created egg record: " . ($egg->crate_count ?? 'ID ' . $egg->id),
            ]);
        }
    }

    public function updated(Egg $egg)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'update',
                'model_type' => Egg::class,
                'model_id'   => $egg->id,
                'description'=> "Updated egg record: " . ($egg->crate_count ?? 'ID ' . $egg->id),
            ]);
        }
    }

    public function deleted(Egg $egg)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'delete',
                'model_type' => Egg::class,
                'model_id'   => $egg->id,
                'description'=> "Deleted egg record: " . ($egg->crate_count ?? 'ID ' . $egg->id),
            ]);
        }
    }

    public function restored(Egg $egg)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'restore',
                'model_type' => Egg::class,
                'model_id'   => $egg->id,
                'description'=> "Restored egg record: " . ($egg->crate_count ?? 'ID ' . $egg->id),
            ]);
        }
    }

    public function forceDeleted(Egg $egg)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'force_delete',
                'model_type' => Egg::class,
                'model_id'   => $egg->id,
                'description'=> "Force deleted egg record: " . ($egg->crate_count ?? 'ID ' . $egg->id),
            ]);
        }
    }
}
