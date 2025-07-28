<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'category',
        'description',
        'amount',
        'date',
        'synced_at',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'synced_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected $dates = ['deleted_at'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
