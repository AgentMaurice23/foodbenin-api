<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    |
    | Champs pouvant être remplis lors de la création d'une commande.
    |
    */

    protected $fillable = [

        'order_number',

        'restaurant_id',

        'user_id',

        'address_id',

        'subtotal',

        'delivery_fee',

        'discount',

        'total',

        'status',

        'notes',

        'uuid',

        'is_paid',

        'estimated_delivery_at',

        'delivered_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'status' => OrderStatusEnum::class,

        'subtotal' => 'decimal:2',

        'delivery_fee' => 'decimal:2',

        'discount' => 'decimal:2',

        'total' => 'decimal:2',

        'is_paid' => 'boolean',

        'estimated_delivery_at' => 'datetime',

        'delivered_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Payment
    |--------------------------------------------------------------------------
    |
    | Une commande possède un paiement.
    |
    */

    public function payment()
    {
        return $this->hasOne(
            Payment::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Commission
    |--------------------------------------------------------------------------
    |
    | Une commande possède une commission.
    |
    */

    public function commission()
    {
        return $this->hasOne(
            Commission::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delivery
    |--------------------------------------------------------------------------
    |
    | Une commande possède une seule livraison.
    |
    */

    public function delivery()
    {
        return $this->hasOne(
            Delivery::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Restaurant
    |--------------------------------------------------------------------------
    */

    public function restaurant()
    {
        return $this->belongsTo(
            Restaurant::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Client
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Address
    |--------------------------------------------------------------------------
    */

    public function address()
    {
        return $this->belongsTo(
            Address::class
        );
    }

    /**
     * Get notifications associated with this order.
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(
            Notification::class,
            'notifiable'
        );
    }
}