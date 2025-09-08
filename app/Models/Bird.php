<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bird extends Model
{
    use SoftDeletes;

    protected $table = 'birds';

    protected $fillable = [
        'breed',
        'type',
        'quantity',
        'quantity_bought',
        'feed_amount',
        'alive',
        'dead',
        'purchase_date',
        'cost',
        'working',
        'age',
        'entry_date',
        'vaccination_status',
        'pen_id',          // <- changed from 'pen' to 'pen_id'
        'stage',
        'synced_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'purchase_date' => 'date',
        'synced_at' => 'datetime',
        'vaccination_status' => 'boolean',
        'working' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function sales()
    {
        return $this->morphMany(Sale::class, 'saleable');
    }

    // Birds belong to a Pen (pen_id FK)
    public function pen()
    {
        return $this->belongsTo(Pen::class, 'pen_id');
    }

    // convenience: get eggs associated with the same pen
    public function eggs()
    {
        return $this->hasMany(Egg::class, 'pen_id');
    }

    // Scope: only birds with quantity > 0 and not soft-deleted
    public function scopeAvailable($query)
    {
        return $query->whereNull('deleted_at')->where('quantity', '>', 0);
    }

    // Nicely formatted display name for selects
    public function displayName(): string
    {
        $parts = [$this->breed, $this->type];
        if ($this->stage) $parts[] = $this->stage;
        $parts[] = "{$this->quantity} available";
        return implode(' — ', $parts);
    }
}
