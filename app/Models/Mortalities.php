<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mortalities extends Model
{
    protected $fillable = ['flock_id', 'date', 'quantity', 'cause'];
}
