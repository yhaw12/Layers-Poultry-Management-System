<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

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
        'created_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    // status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_PAID = 'paid';
    public const STATUS_OVERDUE = 'overdue';

    // Dates
    protected $dates = [
        'sale_date',
        'due_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /* --------------------
     | Relationships
     |-------------------- */

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

    /* --------------------
     | Helpers & business logic
     |-------------------- */

    /**
     * Recalculate paid amount from payments up to $asOf (defaults to now).
     * This persists the paid_amount column only if the recalculated value changed.
     *
     * By default this excludes payments with a payment_date in the future.
     *
     * @param \DateTimeInterface|null $asOf
     * @return float recalculated paid amount
     */
    public function recalculatePaidAmount(\DateTimeInterface $asOf = null): float
    {
        try {
            $asOf = $asOf ? Carbon::parse($asOf) : Carbon::now();

            // Sum payments where payment_date <= asOf (exclude future-dated payments)
            $paid = (float) $this->payments()
                ->whereDate('payment_date', '<=', $asOf->toDateString())
                ->sum('amount');

            $paid = round(max(0, $paid), 2);

            if (round((float)$this->paid_amount, 2) !== $paid) {
                // update column and persist
                $this->paid_amount = $paid;
                $this->save();
            }

            return $paid;
        } catch (\Throwable $e) {
            Log::error('Failed to recalculate paid amount for sale', [
                'sale_id' => $this->id ?? null,
                'error' => $e->getMessage(),
            ]);
            return (float) $this->paid_amount;
        }
    }

    /**
     * Update the sale status based on paid_amount, due_date and asOf (defaults to now).
     * Saves only when status changes.
     *
     * @param \DateTimeInterface|null $asOf
     * @return void
     */
    public function updatePaymentStatus(\DateTimeInterface $asOf = null): void
    {
        try {
            $asOf = $asOf ? Carbon::parse($asOf) : Carbon::now();
            $this->recalculatePaidAmount($asOf);

            $paid = round((float)$this->paid_amount, 2);
            $total = round((float)$this->total_amount, 2);

            if ($total > 0 && $paid >= $total) {
                $newStatus = self::STATUS_PAID;
            } elseif ($paid > 0 && $paid < $total) {
                $newStatus = self::STATUS_PARTIALLY_PAID;
            } else {
                if ($this->due_date && Carbon::parse($this->due_date)->endOfDay()->isPast()) {
                    $newStatus = self::STATUS_OVERDUE;
                } else {
                    $newStatus = self::STATUS_PENDING;
                }
            }

            if ($this->status !== $newStatus) {
                $this->status = $newStatus;
                $this->save();
            }
        } catch (\Throwable $e) {
            Log::error('Status update failed: ' . $e->getMessage());
        }
    }

    /**
     * Return remaining balance (clamped to zero).
     *
     * @return float
     */
    public function balance(): float
    {
        return max(0, round((float)$this->total_amount - (float)$this->paid_amount, 2));
    }

    /**
     * Return true if sale is fully paid (based on status or numeric comparison).
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID
            || round((float)$this->paid_amount, 2) >= round((float)$this->total_amount, 2);
    }

    // use App\Models\User; at top if not already

    public function cashier()
    {
        return $this->belongsTo(\App\Models\User::class, 'cashier_id');
    }
    public function getCashierNameAttribute()
    {
        return $this->cashier ? $this->cashier->name : null;
    }

        public function items() {
        return $this->hasMany(SaleItem::class);
    }


}
