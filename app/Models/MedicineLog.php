<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicineLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'medicine_logs';

    protected $fillable = [
        'medicine_name', 
        'type', 
        'quantity', 
        'unit', 
        'date', 
        'notes',
        'expense_id' // <--- Add this line
    ];

    protected $casts = ['date' => 'date'];
}