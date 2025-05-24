<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineLog extends Model
{
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
}


