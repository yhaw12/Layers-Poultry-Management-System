<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    protected $table = 'feed';
    protected $fillable = ['type', 'supplier', 'quantity', 'weight', 'purchase_date', 'cost', 'synced_at'];
    protected $casts = ['purchase_date' => 'date', 'synced_at' => 'datetime'];

    public function supplier() {
    return $this->belongsTo(Supplier::class);
    }
}