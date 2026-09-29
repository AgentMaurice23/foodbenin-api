<?php

namespace Database\Factories;

use App\Enums\DeliveryStatusEnum;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Delivery>
 */
class DeliveryFactory extends Factory
{
    /**
     * Le modèle associé à cette Factory.
     */
    protected $model = Delivery::class;

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
            | Commande
            |--------------------------------------------------------------------------
            |
            | Une livraison appartient obligatoirement à une commande.
            |
            */

            'order_id' => Order::factory(),

            /*
            |--------------------------------------------------------------------------
            | Livreur
            |--------------------------------------------------------------------------
            |
            | NULL signifie qu'aucun livreur n'est encore assigné.
            |
            */

            'driver_id' => null,

            /*
            |--------------------------------------------------------------------------
            | Statut
            |--------------------------------------------------------------------------
            */

            'status' => DeliveryStatusEnum::PENDING,

            /*
            |--------------------------------------------------------------------------
            | Tarification
            |--------------------------------------------------------------------------
            */

            'delivery_fee' => 1000,

            'driver_earning' => 700,

            'platform_commission' => 300,

            /*
            |--------------------------------------------------------------------------
            | Estimation
            |--------------------------------------------------------------------------
            */

            'estimated_distance' => 5.50,

            'estimated_duration' => 25,

            /*
            |--------------------------------------------------------------------------
            | Horodatages
            |--------------------------------------------------------------------------
            */

            'assigned_at' => null,

            'accepted_at' => null,

            'going_to_restaurant_at' => null,

            'arrived_restaurant_at' => null,

            'picked_up_at' => null,

            'on_the_way_at' => null,

            'arrived_at' => null,

            'delivered_at' => null,

            'failed_at' => null,

            'cancelled_at' => null,

            /*
            |--------------------------------------------------------------------------
            | Raisons
            |--------------------------------------------------------------------------
            */

            'cancel_reason' => null,

            'failure_reason' => null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | État : avec livreur
    |--------------------------------------------------------------------------
    |
    | Permet de générer facilement une livraison déjà assignée
    | à un livreur.
    |
    */

    public function withDriver(): static
    {
        return $this->state(fn (array $attributes) => [

            'driver_id' => Driver::factory(),

            'status' => DeliveryStatusEnum::ASSIGNED,

            'assigned_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | État : livraison acceptée
    |--------------------------------------------------------------------------
    */

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [

            'driver_id' => Driver::factory(),

            'status' => DeliveryStatusEnum::ACCEPTED,

            'assigned_at' => now()->subMinutes(10),

            'accepted_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | État : livraison en cours
    |--------------------------------------------------------------------------
    */

    public function onTheWay(): static
    {
        return $this->state(fn (array $attributes) => [

            'driver_id' => Driver::factory(),

            'status' => DeliveryStatusEnum::ON_THE_WAY,

            'assigned_at' => now()->subMinutes(30),

            'accepted_at' => now()->subMinutes(25),

            'going_to_restaurant_at' => now()->subMinutes(20),

            'arrived_restaurant_at' => now()->subMinutes(10),

            'picked_up_at' => now()->subMinutes(7),

            'on_the_way_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | État : livraison terminée
    |--------------------------------------------------------------------------
    */

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [

            'driver_id' => Driver::factory(),

            'status' => DeliveryStatusEnum::DELIVERED,

            'assigned_at' => now()->subMinutes(45),

            'accepted_at' => now()->subMinutes(40),

            'going_to_restaurant_at' => now()->subMinutes(35),

            'arrived_restaurant_at' => now()->subMinutes(25),

            'picked_up_at' => now()->subMinutes(20),

            'on_the_way_at' => now()->subMinutes(18),

            'arrived_at' => now()->subMinutes(2),

            'delivered_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | État : livraison échouée
    |--------------------------------------------------------------------------
    */

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [

            'driver_id' => Driver::factory(),

            'status' => DeliveryStatusEnum::FAILED,

            'failed_at' => now(),

            'failure_reason' => 'Client injoignable.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | État : livraison annulée
    |--------------------------------------------------------------------------
    */

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [

            'status' => DeliveryStatusEnum::CANCELLED,

            'cancelled_at' => now(),

            'cancel_reason' => 'Commande annulée.',
        ]);
    }
}