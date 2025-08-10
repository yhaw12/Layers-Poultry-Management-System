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
        'pen_id',
        'crates',
        'additional_eggs',
        'total_eggs',
        'is_cracked',
        'egg_size',
        'date_laid',
        'created_by',
    ];

    protected $casts = [
        'date_laid' => 'date',
        // 'is_cracked' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pen()
    {
        return $this->belongsTo(Pen::class, 'pen_id');
    }

    public function sales()
    {
        return $this->morphMany(Sale::class, 'saleable');
    }
}