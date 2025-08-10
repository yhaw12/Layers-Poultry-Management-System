<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'saleable_type', 'saleable_id', 'customer_id', 'quantity', 'unit_price',
        'total_amount', 'sale_date', 'due_date', 'paid_amount', 'status',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'due_date' => 'date',
        'deleted_at' => 'datetime',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function saleable()
    {
        return $this->morphTo();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function isPaid()
    {
        return $this->paid_amount >= $this->total_amount;
    }

    public function updatePaymentStatus()
    {
        $status = $this->paid_amount >= $this->total_amount ? 'paid' :
                  ($this->paid_amount > 0 ? 'partially_paid' :
                  (now()->gt($this->due_date) ? 'overdue' : 'pending'));
        $this->update(['status' => $status]);
    }
}