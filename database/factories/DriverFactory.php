<?php

namespace Database\Factories;

use App\Enums\DriverStatusEnum;
use App\Enums\VehicleTypeEnum;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    /**
     * Définition des données par défaut.
     */
    public function definition(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Identifiant public
            |--------------------------------------------------------------------------
            */

            'uuid' => (string) Str::uuid(),

            /*
            |--------------------------------------------------------------------------
            | Utilisateur propriétaire du profil Driver
            |--------------------------------------------------------------------------
            |
            | Un Driver doit obligatoirement appartenir à un User.
            |
            */

            'user_id' => User::factory(),

            /*
            |--------------------------------------------------------------------------
            | Statut
            |--------------------------------------------------------------------------
            */

            'status' => DriverStatusEnum::OFFLINE,

            /*
            |--------------------------------------------------------------------------
            | Véhicule
            |--------------------------------------------------------------------------
            */

            'vehicle_type' => VehicleTypeEnum::MOTORBIKE,

            'vehicle_brand' => 'Honda',

            'vehicle_model' => 'Wave',

            'plate_number' => fake()->bothify('AB-###-??'),

            /*
            |--------------------------------------------------------------------------
            | Documents
            |--------------------------------------------------------------------------
            */

            'driver_license' => null,

            'identity_document' => null,

            'insurance_document' => null,

            /*
            |--------------------------------------------------------------------------
            | Statistiques
            |--------------------------------------------------------------------------
            */

            'completed_deliveries' => 0,

            'rating' => 5.00,

            'ratings_count' => 0,

            'total_distance' => 0,

            'total_earnings' => 0,

            /*
            |--------------------------------------------------------------------------
            | Vérification
            |--------------------------------------------------------------------------
            */

            'is_verified' => false,

            'verified_at' => null,

            /*
            |--------------------------------------------------------------------------
            | Dernière position
            |--------------------------------------------------------------------------
            */

            'last_latitude' => null,

            'last_longitude' => null,

            'last_location_at' => null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Driver online
    |--------------------------------------------------------------------------
    */

    public function online(): static
    {
        return $this->state(fn () => [
            'status' => DriverStatusEnum::ONLINE,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Driver busy
    |--------------------------------------------------------------------------
    */

    public function busy(): static
    {
        return $this->state(fn () => [
            'status' => DriverStatusEnum::BUSY,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Driver suspended
    |--------------------------------------------------------------------------
    */

    public function suspended(): static
    {
        return $this->state(fn () => [
            'status' => DriverStatusEnum::SUSPENDED,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Driver vérifié
    |--------------------------------------------------------------------------
    */

    public function verified(): static
    {
        return $this->state(fn () => [
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Driver non vérifié
    |--------------------------------------------------------------------------
    */

    public function unverified(): static
    {
        return $this->state(fn () => [
            'is_verified' => false,
            'verified_at' => null,
        ]);
    }
}