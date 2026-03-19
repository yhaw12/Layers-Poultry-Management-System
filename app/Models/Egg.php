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

    public function saleItems()
    {
        return $this->morphMany(SaleItem::class, 'saleable');
    }

    // Scope: only egg batches with crates > 0


public function scopeAvailable($query)
    {
        return $query->withSum('saleItems as sold_crates', 'quantity')
            ->whereRaw('crates > (
                SELECT COALESCE(SUM(quantity), 0) 
                FROM sale_items 
                WHERE saleable_id = eggs.id 
                AND saleable_type = ?
            )', [self::class]);
    }

    // Nicely formatted display name for selects
    public function displayName(): string
    {
        $parts = [];

        // Identification
        $parts[] = "B-{$this->id}";

        // Calculate remaining crates
        $remainingCrates = $this->crates - ($this->sold_crates ?? 0);
        
        $quantity = "{$remainingCrates}ct";
        if ($this->additional_eggs) {
            $quantity .= " (+{$this->additional_eggs})";
        }
        $parts[] = $quantity;

        // Specifics (Size/Date)
        if ($this->egg_size) {
            $parts[] = ucfirst($this->egg_size);
        }

        $parts[] = $this->date_laid?->diffForHumans() ?? 'No Date';

        return implode(' | ', $parts);
    }
}
