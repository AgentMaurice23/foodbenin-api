<?php

namespace App\Models;

use App\Enums\DeliveryStatusEnum;
use Illuminate\Database\Eloquent\Model;

class DeliveryHistory extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Timestamps
    |--------------------------------------------------------------------------
    |
    | Cette table possède uniquement created_at.
    | Il n'existe donc pas de updated_at.
    |
    */

    public const UPDATED_AT = null;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'delivery_id',

        'status',

        'created_by',

        'comment',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'status' => DeliveryStatusEnum::class,

        'metadata' => 'array',

        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Delivery
    |--------------------------------------------------------------------------
    |
    | L'historique appartient à une livraison.
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
    | Utilisateur ayant provoqué le changement.
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