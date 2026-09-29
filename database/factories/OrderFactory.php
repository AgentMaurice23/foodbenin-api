<?php

namespace Database\Factories;

use App\Enums\OrderStatusEnum;
use App\Models\Address;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Le modèle associé à cette factory.
     */
    protected $model = Order::class;

    /**
     * Définition des données par défaut.
     */
    public function definition(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Numéro de commande
            |--------------------------------------------------------------------------
            */

            'order_number' => 'ORD-' . strtoupper(
                Str::random(10)
            ),

            /*
            |--------------------------------------------------------------------------
            | UUID
            |--------------------------------------------------------------------------
            */

            'uuid' => (string) Str::uuid(),

            /*
            |--------------------------------------------------------------------------
            | Client
            |--------------------------------------------------------------------------
            */

            'user_id' => User::factory(),

            /*
            |--------------------------------------------------------------------------
            | Restaurant
            |--------------------------------------------------------------------------
            */

            'restaurant_id' => Restaurant::factory(),

            /*
            |--------------------------------------------------------------------------
            | Adresse de livraison
            |--------------------------------------------------------------------------
            |
            | L'adresse appartient au même utilisateur.
            |
            */

            'address_id' => Address::factory(),

            /*
            |--------------------------------------------------------------------------
            | Montants
            |--------------------------------------------------------------------------
            */

            'subtotal' => 5000,

            'delivery_fee' => 1000,

            'discount' => 0,

            'total' => 6000,

            /*
            |--------------------------------------------------------------------------
            | Statut
            |--------------------------------------------------------------------------
            |
            | On utilise directement l'Enum.
            |
            */

            'status' => OrderStatusEnum::PENDING,

            /*
            |--------------------------------------------------------------------------
            | Paiement
            |--------------------------------------------------------------------------
            */

            'is_paid' => true,

            /*
            |--------------------------------------------------------------------------
            | Livraison estimée
            |--------------------------------------------------------------------------
            */

            'estimated_delivery_at' => now()->addHour(),

            /*
            |--------------------------------------------------------------------------
            | Livraison effective
            |--------------------------------------------------------------------------
            */

            'delivered_at' => null,

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'notes' => null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Commande acceptée
    |--------------------------------------------------------------------------
    */

    public function accepted(): static
    {
        return $this->state(fn () => [

            'status' => OrderStatusEnum::ACCEPTED,

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Commande en préparation
    |--------------------------------------------------------------------------
    */

    public function preparing(): static
    {
        return $this->state(fn () => [

            'status' => OrderStatusEnum::PREPARING,

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Commande prête pour livraison
    |--------------------------------------------------------------------------
    */

    public function readyForDelivery(): static
    {
        return $this->state(fn () => [

            'status' => OrderStatusEnum::READY_FOR_DELIVERY,

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Commande en cours de livraison
    |--------------------------------------------------------------------------
    */

    public function onTheWay(): static
    {
        return $this->state(fn () => [

            'status' => OrderStatusEnum::ON_THE_WAY,

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Commande livrée
    |--------------------------------------------------------------------------
    */

    public function delivered(): static
    {
        return $this->state(fn () => [

            'status' => OrderStatusEnum::DELIVERED,

            'delivered_at' => now(),

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Commande annulée
    |--------------------------------------------------------------------------
    */

    public function cancelled(): static
    {
        return $this->state(fn () => [

            'status' => OrderStatusEnum::CANCELLED,

        ]);
    }
}

