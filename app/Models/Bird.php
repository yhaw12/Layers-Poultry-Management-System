<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bird extends Model
{
    protected $table = 'birds';
    protected $fillable = ['breed', 'type', 'quantity', 'working', 'age', 'entry_date', 'synced_at'];
    protected $casts = ['entry_date' => 'date', 'synced_at' => 'datetime'];
}