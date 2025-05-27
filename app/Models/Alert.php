<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = ['message', 'type', 'read_at'];
    protected $dates = ['read_at', 'created_at', 'updated_at'];
}