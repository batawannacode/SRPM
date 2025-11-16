<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRule extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'property_id',
        'grace_period_days',
        'penalty_type',
        'penalty_value',
        'auto_apply',
        'notify_tenant',
    ];

    protected $casts = [
        'auto_apply' => 'boolean',
        'notify_tenant' => 'boolean',
        'penalty_value' => 'decimal:2',
    ];

    /**
     * Get the property that owns the payment rule.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

}
