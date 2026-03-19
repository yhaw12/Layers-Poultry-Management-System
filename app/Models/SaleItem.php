<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model {
    protected $fillable = [
        'sale_id', 
        'saleable_id', 
        'saleable_type', 
        'quantity', 
        'unit_price', 
        'subtotal'
    ];

    // Ensure our financial and quantity data behave as numbers, not strings
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // The polymorphic relationship (Links to Egg or Bird)
    public function saleable() {
        return $this->morphTo();
    }

    // The inverse relationship (Links back to the main Sale/Invoice)
    public function sale() {
        return $this->belongsTo(Sale::class);
    }
}