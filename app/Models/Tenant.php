<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    /** @use HasFactory<\Database\Factories\TenantFactory> */
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * Get the user that owns the owner.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the expected payments for the lease.
     */
    public function expectedPayments(): HasMany
    {
        return $this->hasMany(ExpectedPayment::class);
    }

    /**
     * Get the leases for the tenant.
     */
    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }
}
