<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{   
    use SoftDeletes;
    use HasFactory;
    protected $table = 'sales';
    protected $fillable = [
        'customer_id',
        'saleable_id',
        'saleable_type',
        'quantity',
        'unit_price',
        'total_amount',
        'sale_date',
        'product_variant','sale_date', 'due_date', 'status', 'paid_amount',
    ];
    protected $casts = [
        'sale_date' => 'date',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'product_variant' => 'string'
    ];
    protected $dates = ['deleted_at'];

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

    public function updatePaymentStatus()
    {
        $totalPaid = $this->payments()->sum('amount');
        $this->paid_amount = $totalPaid;

        if ($totalPaid >= $this->total_amount) {
            $this->status = 'paid';
        } elseif ($totalPaid > 0) {
            $this->status = 'partially_paid';
        } else {
            $this->status = $this->due_date && Carbon::now()->gt($this->due_date) ? 'overdue' : 'pending';
        }

        $this->save();
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPartiallyPaid()
    {
        return $this->status === 'partially_paid';
    }

    public function isOverdue()
    {
        return $this->status === 'overdue';
    }
}