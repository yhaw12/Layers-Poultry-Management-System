<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chicks extends Model
{
    use SoftDeletes;
    protected $table = 'chicks';
    protected $fillable = ['breed', 'quantity_bought', 'feed_amount', 'alive', 'dead', 'purchase_date', 'cost', 'synced_at'];
    protected $casts = ['purchase_date' => 'date', 'synced_at' => 'datetime'];
    protected $dates = ['deleted_at'];
}