<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use SoftDeletes;
    protected $table = 'income';
    protected $fillable = ['source', 'description', 'amount', 'date', 'synced_at'];
    protected $casts = ['date' => 'date', 'synced_at' => 'datetime'];
    protected $dates = ['deleted_at'];
}