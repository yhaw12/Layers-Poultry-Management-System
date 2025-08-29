<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'saleable_type',
        'saleable_id',
        'customer_id',
        'quantity',
        'unit_price',
        'total_amount',
        'sale_date',
        'due_date',
        'paid_amount',
        'status',
        'product_variant',
        // 'created_by',
    ];

     protected $dates = [
        'sale_date',
        'due_date',
        'created_at',
        'updated_at',
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

    /**
     * Return true if sale is fully paid (with safe float compare).
     */
    public function isPaid(): bool
    {
        return round((float) $this->paid_amount, 2) >= round((float) $this->total_amount, 2);
    }

    /**
     * Update the status based on paid_amount and due_date.
     */
    public function updatePaymentStatus(): void
    {
        $paid = (float) $this->paid_amount;
        $total = (float) $this->total_amount;

        if (round($paid, 2) >= round($total, 2)) {
            $this->status = 'paid';
        } elseif ($paid > 0) {
            $this->status = 'partially_paid';
        } else {
            // not paid yet; check overdue
            if ($this->due_date && Carbon::now()->greaterThan(Carbon::parse($this->due_date))) {
                $this->status = 'overdue';
            } else {
                $this->status = 'pending';
            }
        }

        // Only save if dirty to avoid needless DB writes
        if ($this->isDirty('status')) {
            $this->save();
        }
    }

    /**
     * Convenience: balance remaining
     */
    public function balance(): float
    {
        return max(0, round((float)$this->total_amount - (float)$this->paid_amount, 2));
    }
}