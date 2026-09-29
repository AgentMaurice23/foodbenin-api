<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Extra extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'restaurant_id',

        'uuid',

        'name',

        'price',

        'is_available',

        'position',
    ];

    protected $casts = [

        'price' => 'decimal:2',

        'is_available' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(
            Restaurant::class
        );
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class
        );
    }

    public function scopeAvailable($query)
    {
        return $query->where(
            'is_available',
            true
        );
    }
}