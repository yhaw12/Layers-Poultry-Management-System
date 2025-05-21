<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hen extends Model
{
    protected $table = 'hens';
    protected $fillable = ['breed', 'quantity', 'working', 'age', 'entry_date', 'synced_at'];
    protected $casts = ['entry_date' => 'date', 'synced_at' => 'datetime'];
}