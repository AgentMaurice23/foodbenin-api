<?php

namespace App\Models;

use App\Enums\DeliveryProofTypeEnum;
use Illuminate\Database\Eloquent\Model;

class DeliveryProof extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Timestamps
    |--------------------------------------------------------------------------
    |
    | La table possède uniquement created_at.
    |
    */

    public const UPDATED_AT = null;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'uuid',

        'delivery_id',

        'type',

        'value',

        'metadata',

        'created_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'type' => DeliveryProofTypeEnum::class,

        'metadata' => 'array',

        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Delivery
    |--------------------------------------------------------------------------
    |
    | Une preuve appartient à une livraison.
    |
    */

    public function delivery()
    {
        return $this->belongsTo(
            Delivery::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Creator
    |--------------------------------------------------------------------------
    |
    | Utilisateur ayant enregistré la preuve.
    |
    */

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}