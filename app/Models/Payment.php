<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sale_id', 'customer_id', 'amount', 'payment_date', 'payment_method', 'notes',
        // 'created_by',
    ];

        protected $dates = [
        'payment_date',
        'created_at',
        'updated_at',
    ];


    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}