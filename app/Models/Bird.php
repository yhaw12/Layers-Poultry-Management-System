<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bird extends Model
{
    use SoftDeletes;

    protected $table = 'birds';

    protected $fillable = [
        'breed',
        'type',
        'quantity',
        'quantity_bought',
        'feed_amount',
        'alive',
        'dead',
        'purchase_date',
        'cost',
        'working',
        'age',
        'entry_date',
        'vaccination_status',
        'housing_location',
        'stage',
        'synced_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'purchase_date' => 'date',
        'synced_at' => 'datetime',
        'vaccination_status' => 'boolean',
        'working' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function sales()
    {
        return $this->morphMany(Sale::class, 'saleable');
    }
}