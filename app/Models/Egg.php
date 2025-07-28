<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Egg extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'crates',
        'date_laid',
        'sold_quantity',
        'sold_date',
        'sale_price',
        'created_by',
    ];

    protected $casts = [
        'date_laid' => 'date',
        'sold_date' => 'date',
        'sale_price' => 'decimal:2',
    ];

    protected $dates = ['deleted_at'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
