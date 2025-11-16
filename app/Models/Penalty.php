<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    /** @use HasFactory<\Database\Factories\PenaltyFactory> */
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'expected_payment_id',
        'due_date',
        'amount',
        'reason',
        'is_paid',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the lease that owns the penalty.
     */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /**
     * Get the expected payment that owns the payment.
     */
    public function expectedPayment(): BelongsTo
    {
        return $this->belongsTo(ExpectedPayment::class);
    }
}
