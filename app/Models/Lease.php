<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Lease extends Model
{
    /** @use HasFactory<\Database\Factories\LeaseFactory> */
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'unit_id',
        'tenant_id',
        'status',
        'start_date',
        'end_date',
        'rent_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rent_price' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function terminate()
    {
        $this->status = 'terminated';
        $this->save();

        // Update unit status to vacant
        $this->unit->status = 'vacant';
        $this->unit->save();
    }

    /**
     * Get the unit that owns the lease.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the tenant that owns the lease.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the expected payments for the lease.
     */
    public function expectedPayments(): HasMany
    {
        return $this->hasMany(ExpectedPayment::class);
    }

    /**
     * Get the penalties for the lease.
     */
    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class);
    }

    /**
     * Get the documents for the lease.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
