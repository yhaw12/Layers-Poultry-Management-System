<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Bird extends Model
{
    use SoftDeletes;
    protected $table = 'birds';
    protected $fillable = ['breed', 'type', 'quantity', 'working', 'age', 'entry_date', 'synced_at'];
    protected $casts = ['entry_date' => 'date', 'synced_at' => 'datetime'];
    protected $dates = ['deleted_at'];
}