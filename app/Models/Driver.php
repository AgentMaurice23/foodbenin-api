<?php

namespace App\Models;

use App\Enums\DriverStatusEnum;
use App\Enums\VehicleTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    |
    | Champs pouvant être remplis via create() / update().
    |
    */

    protected $fillable = [

        'uuid',

        'user_id',

        'status',

        'vehicle_type',

        'vehicle_brand',

        'vehicle_model',

        'plate_number',

        'driver_license',

        'identity_document',

        'insurance_document',

        'completed_deliveries',

        'rating',

        'ratings_count',

        'total_distance',

        'total_earnings',

        'is_verified',

        'verified_at',

        'last_latitude',

        'last_longitude',

        'last_location_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    |
    | Les valeurs métier sont automatiquement transformées
    | en Enum ou en type PHP approprié.
    |
    */

    protected $casts = [

        'status' => DriverStatusEnum::class,

        'vehicle_type' => VehicleTypeEnum::class,

        'is_verified' => 'boolean',

        'rating' => 'decimal:2',

        'total_distance' => 'decimal:2',

        'total_earnings' => 'decimal:2',

        'last_latitude' => 'decimal:7',

        'last_longitude' => 'decimal:7',

        'verified_at' => 'datetime',

        'last_location_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Driver $driver) {

            if (empty($driver->uuid)) {
                $driver->uuid = (string) Str::uuid();
            }

        });
    }
    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    |
    | Un Driver appartient à un User.
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
    | Deliveries
    |--------------------------------------------------------------------------
    |
    | Un Driver peut effectuer plusieurs livraisons
    | au cours de son activité.
    |
    */

    public function deliveries()
    {
        return $this->hasMany(
            Delivery::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Locations
    |--------------------------------------------------------------------------
    |
    | Historique des positions GPS du livreur.
    |
    */

    public function locations()
    {
        return $this->hasMany(
            DriverLocation::class
        );
    }
}