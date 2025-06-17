<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;
    protected $fillable = ['category', 'description', 'amount', 'date'];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];
    protected $dates = ['deleted_at'];
}