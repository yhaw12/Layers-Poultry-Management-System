<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;
    protected $table = 'sales';
    protected $fillable = ['customer_id', 'saleable_id', 'saleable_type', 'quantity', 'unit_price', 'total_amount', 'sale_date'];
    protected $casts = ['sale_date' => 'date', 'unit_price' => 'decimal:2', 'total_amount' => 'decimal:2'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function saleable()
    {
        return $this->morphTo();
    }
}