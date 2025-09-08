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
        // $pen = optional($this->pen)->name;
        $date = optional($this->date_laid)->format('Y-m-d');
        $extra = $this->additional_eggs ? "+{$this->additional_eggs} eggs" : null;
        $parts = ["Batch #{$this->id}", $date, "{$this->crates} crates"];
        // if ($pen) $parts[] = $pen;
        if ($extra) $parts[] = $extra;
        return implode(' — ', array_filter($parts));
    }
}
