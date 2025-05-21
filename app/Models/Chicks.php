<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chicks extends Model
{
    protected $table = 'chicks';
    protected $fillable = ['breed', 'quantity_bought', 'feed_amount', 'alive', 'dead', 'purchase_date', 'cost', 'synced_at'];
    protected $casts = ['purchase_date' => 'date', 'synced_at' => 'datetime'];
}