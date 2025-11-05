<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    /** @use HasFactory<\Database\Factories\UnitFactory> */
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'property_id',
        'unit_number',
        'status',
    ];

     /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        //
    ];

    /**
     * Get the property that owns the unit.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the leases for the unit.
     */
    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }
}
