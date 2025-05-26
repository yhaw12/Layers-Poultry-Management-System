<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Egg extends Model
{
    protected $table = 'eggs';
    protected $fillable = ['crates', 'date_laid', 'sold_quantity', 'sold_date', 'sale_price', 'synced_at'];
    protected $casts = ['date_laid' => 'date', 'sold_date' => 'date', 'synced_at' => 'datetime'];
}
