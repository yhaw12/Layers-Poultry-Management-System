<?php

namespace App\Observers;

use App\Models\Bird;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class BirdObserver
{
    public function created(Bird $bird)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'create',
                'model_type' => Bird::class,
                'model_id'   => $bird->id,
                'description'=> "Created bird record: " . ($bird->type ?? 'ID ' . $bird->id),
            ]);
        }
    }

    public function updated(Bird $bird)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'update',
                'model_type' => Bird::class,
                'model_id'   => $bird->id,
                'description'=> "Updated bird record: " . ($bird->type ?? 'ID ' . $bird->id),
            ]);
        }
    }

    public function deleted(Bird $bird)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'delete',
                'model_type' => Bird::class,
                'model_id'   => $bird->id,
                'description'=> "Deleted bird record: " . ($bird->type ?? 'ID ' . $bird->id),
            ]);
        }
    }

    public function restored(Bird $bird)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'restore',
                'model_type' => Bird::class,
                'model_id'   => $bird->id,
                'description'=> "Restored bird record: " . ($bird->type ?? 'ID ' . $bird->id),
            ]);
        }
    }

    public function forceDeleted(Bird $bird)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'force_delete',
                'model_type' => Bird::class,
                'model_id'   => $bird->id,
                'description'=> "Force deleted bird record: " . ($bird->type ?? 'ID ' . $bird->id),
            ]);
        }
    }
}
