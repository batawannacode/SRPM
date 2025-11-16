<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
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

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->qr_image_path);
    }

    public function getTenantProofPreviewUrlsAttribute(): string
    {
        $encrypted = Crypt::encryptString($this->qr_image_path);

        return URL::temporarySignedRoute(
            'tenant.file.preview',
            now()->addMinutes(5),
            ['encrypted' => base64_encode($encrypted)]
        );
    }

    /**
     * Get the owner that owns the payment method.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

}