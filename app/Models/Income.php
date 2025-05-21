<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = 'income';
    protected $fillable = ['source', 'description', 'amount', 'date', 'synced_at'];
    protected $casts = ['date' => 'date', 'synced_at' => 'datetime'];
}