<?php

namespace App\Observers;

use App\Models\Feed;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class FeedObserver
{
    public function created(Feed $feed)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'create',
                'model_type' => Feed::class,
                'model_id'   => $feed->id,
                'description'=> "Created feed record: " . ($feed->name ?? 'ID ' . $feed->id),
            ]);
        }
    }

    public function updated(Feed $feed)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'update',
                'model_type' => Feed::class,
                'model_id'   => $feed->id,
                'description'=> "Updated feed record: " . ($feed->name ?? 'ID ' . $feed->id),
            ]);
        }
    }

    public function deleted(Feed $feed)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'delete',
                'model_type' => Feed::class,
                'model_id'   => $feed->id,
                'description'=> "Deleted feed record: " . ($feed->name ?? 'ID ' . $feed->id),
            ]);
        }
    }

    public function restored(Feed $feed)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'restore',
                'model_type' => Feed::class,
                'model_id'   => $feed->id,
                'description'=> "Restored feed record: " . ($feed->name ?? 'ID ' . $feed->id),
            ]);
        }
    }

    public function forceDeleted(Feed $feed)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'force_delete',
                'model_type' => Feed::class,
                'model_id'   => $feed->id,
                'description'=> "Force deleted feed record: " . ($feed->name ?? 'ID ' . $feed->id),
            ]);
        }
    }
}
