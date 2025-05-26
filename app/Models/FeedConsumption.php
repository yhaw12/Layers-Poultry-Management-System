<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedConsumption extends Model
{
    protected $fillable = ['feed_id', 'date', 'quantity'];

    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }
}