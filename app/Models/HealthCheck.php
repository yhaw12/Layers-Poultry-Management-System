<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthCheck extends Model
{
    protected $fillable = ['bird_id', 'date', 'status', 'symptoms', 'treatment', 'notes'];

    protected $casts = [
        'date' => 'date',
    ];

    public function bird()
    {
        return $this->belongsTo(Bird::class);
    }
}