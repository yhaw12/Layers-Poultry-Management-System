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

    // Scope: only egg batches with crates > 0
    public function scopeAvailable($query)
    {
        return $query->whereNull('deleted_at')->where('crates', '>', 0);
    }

    // Nicely formatted display name for selects
    public function displayName(): string
{
    $parts = [];

    // 1. Identification
    $parts[] = "B-{$this->id}";

    // 2. The most important metric (Quantity)
    $quantity = "{$this->crates}ct";
    if ($this->additional_eggs) {
        $quantity .= " (+{$this->additional_eggs})";
    }
    $parts[] = $quantity;

    // 3. Specifics (Size/Date)
    if ($this->egg_size) {
        $parts[] = $this->egg_size;
    }

    $parts[] = $this->date_laid?->diffForHumans() ?? 'No Date';

    // Result: B-102 — 12ct (+5) — LARGE — 2 days ago
    return implode(' | ', $parts);
}
}
