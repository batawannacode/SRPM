<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'owner_id',
        'type',
        'account_name',
        'account_number',
        'qr_image_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];

    /**
     * Get the owner that owns the payment method.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

}
