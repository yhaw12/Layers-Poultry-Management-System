<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'status', 'total_amount'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function sales() { return $this->hasMany(Sale::class); }
}