<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'flock_id', 'date_sold', 'quantity', 'unit_price',
        'customer_name','customer_contact',
    ];

    public function flock()
    {
        return $this->belongsTo(Flock::class);
    }

    // total revenue helper
    public function getRevenueAttribute()
    {
        return $this->quantity * $this->unit_price;
    }
}