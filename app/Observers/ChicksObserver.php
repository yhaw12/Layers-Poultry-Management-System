<?php

namespace App\Observers;

use App\Models\Chicks;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class ChicksObserver
{
    public function created(Chicks $chick)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'create',
                'model_type' => Chicks::class,
                'model_id'   => $chick->id,
                'description'=> "Created chick record: " . ($chick->batch_name ?? 'ID ' . $chick->id),
            ]);
        }
    }

    public function updated(Chicks $chick)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'update',
                'model_type' => Chicks::class,
                'model_id'   => $chick->id,
                'description'=> "Updated chick record: " . ($chick->batch_name ?? 'ID ' . $chick->id),
            ]);
        }
    }

    public function deleted(Chicks $chick)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'delete',
                'model_type' => Chicks::class,
                'model_id'   => $chick->id,
                'description'=> "Deleted chick record: " . ($chick->batch_name ?? 'ID ' . $chick->id),
            ]);
        }
    }

    public function restored(Chicks $chick)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'restore',
                'model_type' => Chicks::class,
                'model_id'   => $chick->id,
                'description'=> "Restored chick record: " . ($chick->batch_name ?? 'ID ' . $chick->id),
            ]);
        }
    }

    public function forceDeleted(Chicks $chick)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'force_delete',
                'model_type' => Chicks::class,
                'model_id'   => $chick->id,
                'description'=> "Force deleted chick record: " . ($chick->batch_name ?? 'ID ' . $chick->id),
            ]);
        }
    }
}
