<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
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
        'proof' => 'array',
    ];

    public function getProofPreviewUrlsAttribute(): array
    {
        if (!is_array($this->proof)) {
            return [];
        }

        return collect($this->proof)->map(function ($path) {
            $encrypted = Crypt::encryptString($path);

            return URL::temporarySignedRoute(
                'owner.file.preview',
                now()->addMinutes(5),
                ['encrypted' => base64_encode($encrypted)]
            );
        })->toArray();
    }

    public function getTenantProofPreviewUrlsAttribute(): array
    {
        if (!is_array($this->proof)) {
            return [];
        }

        return collect($this->proof)->map(function ($path) {
            $encrypted = Crypt::encryptString($path);

            return URL::temporarySignedRoute(
                'tenant.file.preview',
                now()->addMinutes(5),
                ['encrypted' => base64_encode($encrypted)]
            );
        })->toArray();
    }

    /**
     * Get the expected payment that owns the payment.
     */
    public function expectedPayment(): BelongsTo
    {
        return $this->belongsTo(ExpectedPayment::class);
    }
}