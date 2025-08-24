<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'type','title','message','due_date','severity','meta','is_done'
    ];

    protected $casts = [
        'meta' => 'array',
        'is_done' => 'boolean',
        'due_date' => 'date',
    ];
}
