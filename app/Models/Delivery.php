<?php

namespace App\Models;

use App\Enums\DeliveryStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'uuid',

        'order_id',

        'driver_id',

        'status',

        'delivery_fee',

        'driver_earning',

        'platform_commission',

        'estimated_distance',

        'estimated_duration',

        'assigned_at',

        'accepted_at',

        'going_to_restaurant_at',

        'arrived_restaurant_at',

        'picked_up_at',

        'on_the_way_at',

        'arrived_at',

        'delivered_at',

        'failed_at',

        'cancelled_at',

        'cancel_reason',

        'failure_reason',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'status' => DeliveryStatusEnum::class,

        'delivery_fee' => 'decimal:2',

        'driver_earning' => 'decimal:2',

        'platform_commission' => 'decimal:2',

        'estimated_distance' => 'decimal:2',

        'estimated_duration' => 'integer',

        'assigned_at' => 'datetime',

        'accepted_at' => 'datetime',

        'going_to_restaurant_at' => 'datetime',

        'arrived_restaurant_at' => 'datetime',

        'picked_up_at' => 'datetime',

        'on_the_way_at' => 'datetime',

        'arrived_at' => 'datetime',

        'delivered_at' => 'datetime',

        'failed_at' => 'datetime',

        'cancelled_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Order
    |--------------------------------------------------------------------------
    |
    | Une livraison appartient à une commande.
    |
    */

    public function order()
    {
        return $this->belongsTo(
            Order::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | Une livraison peut ne pas encore avoir de livreur.
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
    | Histories
    |--------------------------------------------------------------------------
    |
    | Une livraison possède un historique de changements
    | de statut.
    |
    */

    public function histories()
    {
        return $this->hasMany(
            DeliveryHistory::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Locations
    |--------------------------------------------------------------------------
    |
    | Positions GPS enregistrées pendant la livraison.
    |--------------------------------------------------------------------------
    */

    public function locations()
    {
        return $this->hasMany(
            DriverLocation::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Proof
    |--------------------------------------------------------------------------
    |
    | Retourne toutes les preuves associées à la livraison.
    |
    */

    public function proofs()
    {
        return $this->hasMany(
            DeliveryProof::class
        );
    }
}