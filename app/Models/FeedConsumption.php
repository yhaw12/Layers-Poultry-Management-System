<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedConsumption extends Model
{
    use SoftDeletes;
    protected $table = 'feed_consumption';
    protected $fillable = ['feed_id', 'date', 'quantity'];
    protected $casts = ['date' => 'date'];

    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }
    protected $dates = ['deleted_at'];
}