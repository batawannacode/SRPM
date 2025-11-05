<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'expected_payment_id',
        'amount',
        'payment_method',
        'account_name',
        'account_number',
        'reference_number',
        'proof',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the expected payment that owns the payment.
     */
    public function expectedPayment(): BelongsTo
    {
        return $this->belongsTo(ExpectedPayment::class);
    }
}
