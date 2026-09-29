<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    |
    | Champs pouvant être remplis via create() / update().
    |
    */

    protected $fillable = [

        'user_id',

        'title',

        'recipient_name',

        'recipient_phone',

        'address',

        'latitude',

        'longitude',

        'is_default',

    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'latitude' => 'decimal:7',

        'longitude' => 'decimal:7',

        'is_default' => 'boolean',

    ];

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    |
    | Une adresse appartient à un utilisateur.
    |
    */

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    |
    | Une adresse peut être utilisée par plusieurs commandes.
    |
    */

    public function orders()
    {
        return $this->hasMany(
            Order::class,
            'address_id'
        );
    }
}