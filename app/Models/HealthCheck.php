<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_date',
        'status',
    ];

    protected $casts = [
        'check_date' => 'date',
    ];
}
