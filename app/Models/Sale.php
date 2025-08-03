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

    protected $dates = ['sale_date', 'due_date', 'deleted_at'];

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