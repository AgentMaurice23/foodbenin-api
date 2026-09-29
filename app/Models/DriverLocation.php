<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Timestamps
    |--------------------------------------------------------------------------
    |
    | Nous avons uniquement created_at dans la migration.
    |
    */

    public const UPDATED_AT = null;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'driver_id',

        'delivery_id',

        'latitude',

        'longitude',

        'accuracy',

        'speed',

        'heading',

        'recorded_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'latitude' => 'decimal:7',

        'longitude' => 'decimal:7',

        'accuracy' => 'decimal:2',

        'speed' => 'decimal:2',

        'heading' => 'decimal:2',

        'recorded_at' => 'datetime',

        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | Une position appartient à un livreur.
    |
    */

    public function driver()
    {
        return $this->belongsTo(
            Driver::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delivery
    |--------------------------------------------------------------------------
    |
    | Une position peut être liée à une livraison.
    |
    */

    public function delivery()
    {
        return $this->belongsTo(
            Delivery::class
        );
    }
}