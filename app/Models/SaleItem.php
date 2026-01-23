<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model {
    protected $fillable = ['sale_id', 'saleable_id', 'saleable_type', 'quantity', 'unit_price', 'subtotal'];

    public function saleable() {
        return $this->morphTo();
    }
}