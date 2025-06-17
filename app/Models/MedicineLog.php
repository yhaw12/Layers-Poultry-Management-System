<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicineLog extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'medicine_name',
        'type',
        'quantity',
        'unit',
        'date',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];
    protected $dates = ['deleted_at'];
}


