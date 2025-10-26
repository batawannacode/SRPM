<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyFactory> */
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'owner_id',
        'name',
        'address',
        'description',
        'total_units',
    ];

    /**
     * Get the owner that owns the property.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}