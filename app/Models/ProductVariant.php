<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'product_id',

        'uuid',

        'name',

        'price',

        'is_default',

        'is_available',

        'position',
    ];

    protected $casts = [

        'price' => 'decimal:2',

        'is_default' => 'boolean',

        'is_available' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(
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