<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory;

        /**
        * The attributes that are mass assignable.
        *
        * @var list<string>
        */
    protected $fillable = [
        'lease_id',
        'file_name',
        'file_path',
    ];

    public function getTemporaryPreviewUrlAttribute(): string
    {
        $encrypted = Crypt::encryptString($this->file_path);

        return URL::temporarySignedRoute(
            'owner.file.preview',
            now()->addMinutes(5),
            ['encrypted' => base64_encode($encrypted)]
        );
    }

    public function getTenantTemporaryPreviewUrlAttribute(): string
    {
        $encrypted = Crypt::encryptString($this->file_path);

        return URL::temporarySignedRoute(
            'tenant.file.preview',
            now()->addMinutes(5),
            ['encrypted' => base64_encode($encrypted)]
        );
    }

    /**
     * Get the lease that owns the document.
     */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
