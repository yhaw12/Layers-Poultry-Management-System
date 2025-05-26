<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mortalities extends Model
{
    protected $fillable = ['date', 'quantity', 'cause'];
}