<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{ 
    use SoftDeletes;
    use HasFactory; 

    protected $fillable = ['name', 'contact', 'email'];
    protected $dates = ['deleted_at'];
}