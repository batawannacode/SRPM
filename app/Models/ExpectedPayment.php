<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ExpectedPayment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'lease_id',
        'payment_date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_date' => 'datetime',
    ];

    /**
     * Get the lease that owns the payment.
     */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /**
     * Get the payment for the expected payment.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
