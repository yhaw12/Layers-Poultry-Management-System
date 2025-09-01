<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'customer_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /* --------------------
     | Relationships
     |-------------------- */

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /* --------------------
     | Model events: keep sale consistent
     |-------------------- */

    protected static function booted()
    {
        // when a payment is created/updated/deleted ensure sale paid_amount & status are in sync.
        static::created(function (Payment $payment) {
            try {
                $sale = Sale::withTrashed()->find($payment->sale_id);
                if ($sale) {
                    // use payment_date of created payment as "asOf" so future-dated payments behave as desired
                    $asOf = Carbon::parse($payment->payment_date ?? now());
                    $sale->recalculatePaidAmount($asOf);
                    $sale->updatePaymentStatus($asOf);
                }
            } catch (\Throwable $e) {
                Log::error('Error in Payment::created hook', [
                    'payment_id' => $payment->id ?? null,
                    'sale_id' => $payment->sale_id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        static::updated(function (Payment $payment) {
            try {
                $sale = Sale::withTrashed()->find($payment->sale_id);
                if ($sale) {
                    // choose the earliest relevant asOf between now and payment_date
                    $asOf = Carbon::parse($payment->payment_date ?? now());
                    $sale->recalculatePaidAmount($asOf);
                    $sale->updatePaymentStatus($asOf);
                }
            } catch (\Throwable $e) {
                Log::error('Error in Payment::updated hook', [
                    'payment_id' => $payment->id ?? null,
                    'sale_id' => $payment->sale_id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        static::deleted(function (Payment $payment) {
            try {
                // payment may be deleted; use sale_id to find related sale
                $sale = Sale::withTrashed()->find($payment->sale_id);
                if ($sale) {
                    // recalc as of now
                    $sale->recalculatePaidAmount();
                    $sale->updatePaymentStatus();
                }
            } catch (\Throwable $e) {
                Log::error('Error in Payment::deleted hook', [
                    'payment_id' => $payment->id ?? null,
                    'sale_id' => $payment->sale_id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }
}
