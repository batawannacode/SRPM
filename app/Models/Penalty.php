<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    /** @use HasFactory<\Database\Factories\PenaltyFactory> */
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'lease_id',
        'due_date',
        'amount',
        'reason',
        'is_paid',
    ];

    /**
     * Get the lease that owns the penalty.
     */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
