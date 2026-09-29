<?php

namespace Tests\Feature;

use App\Enums\DeliveryProofTypeEnum;
use App\Enums\DeliveryStatusEnum;
use App\Enums\DriverStatusEnum;
use App\Enums\VehicleTypeEnum;
use App\Models\City;
use App\Models\Delivery;
use App\Models\DeliveryHistory;
use App\Models\DeliveryProof;
use App\Models\Driver;
use App\Models\DriverLocation;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeliveryRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Helper : créer un contexte Restaurant
    |--------------------------------------------------------------------------
    |
    | Une commande FoodBenin appartient obligatoirement à un restaurant.
    | Le restaurant nécessite lui-même un owner, une ville et une zone.
    |
    */

    private function createRestaurant(): Restaurant
    {
        /*
        |--------------------------------------------------------------------------
        | Création du propriétaire
        |--------------------------------------------------------------------------
        */

        $owner = User::factory()->create();

        /*
        |--------------------------------------------------------------------------
        | Création de la ville
        |--------------------------------------------------------------------------
        */

        $city = City::create([

            'name' => 'Cotonou',

            'slug' => 'cotonou',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Création de la zone
        |--------------------------------------------------------------------------
        */

        $zone = Zone::create([

            'city_id' => $city->id,

            'name' => 'Gbegamey',

            'slug' => 'gbegamey',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Création du restaurant
        |--------------------------------------------------------------------------
        */

        return Restaurant::create([

            'owner_id' => $owner->id,

            'city_id' => $city->id,

            'zone_id' => $zone->id,

            'name' => 'Restaurant Test',

            'slug' => 'restaurant-test',

            'description' => 'Restaurant utilisé pour les tests.',

            'phone' => '97000000',

            'email' => 'test@restaurant.test',

            'address' => 'Gbegamey',

            'latitude' => 6.3703,

            'longitude' => 2.3912,

            'rating' => 0,

            'reviews_count' => 0,

            'delivery_fee' => 1000,

            'minimum_order' => 0,

            'uuid' => (string) Str::uuid(),

            'is_open' => true,

            'is_verified' => true,

            'status' => 'approved',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper : créer une commande
    |--------------------------------------------------------------------------
    |
    | Cette méthode respecte exactement les contraintes de ta table orders.
    |
    */

    private function createOrder(
        Restaurant $restaurant
    ): Order {
        /*
        |--------------------------------------------------------------------------
        | Création du client
        |--------------------------------------------------------------------------
        */

        $client = User::factory()->create();

        /*
        |--------------------------------------------------------------------------
        | Création de la commande
        |--------------------------------------------------------------------------
        */

        return Order::create([

            'order_number' =>
                'TEST-' . strtoupper(
                    Str::random(10)
                ),

            'restaurant_id' =>
                $restaurant->id,

            'user_id' =>
                $client->id,

            'address_id' =>
                null,

            'subtotal' =>
                5000,

            'delivery_fee' =>
                1000,

            'discount' =>
                0,

            'total' =>
                6000,

            'status' =>
                'pending',

            'notes' =>
                'Commande de test.',

            'uuid' =>
                (string) Str::uuid(),

            'is_paid' =>
                true,

            'estimated_delivery_at' =>
                null,

            'delivered_at' =>
                null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Test 1
    |--------------------------------------------------------------------------
    |
    | Vérifie qu'un utilisateur peut avoir un profil Driver.
    |
    */

    public function test_user_can_have_one_driver(): void
    {
        $user = User::factory()->create();

        $driver = Driver::create([

            'uuid' =>
                (string) Str::uuid(),

            'user_id' =>
                $user->id,

            'status' =>
                DriverStatusEnum::OFFLINE,

            'vehicle_type' =>
                VehicleTypeEnum::MOTORBIKE,

            'is_verified' =>
                false,

            'rating' =>
                5.00,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Vérification User → Driver
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $user->driver->is($driver)
        );

        /*
        |--------------------------------------------------------------------------
        | Vérification Driver → User
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $driver->user->is($user)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Test 2
    |--------------------------------------------------------------------------
    |
    | Vérifie qu'un Driver peut être associé à plusieurs livraisons
    | au cours de son activité.
    |
    */

    public function test_driver_can_have_multiple_deliveries(): void
    {
        $user = User::factory()->create();

        $driver = Driver::create([

            'uuid' =>
                (string) Str::uuid(),

            'user_id' =>
                $user->id,

            'status' =>
                DriverStatusEnum::OFFLINE,

            'vehicle_type' =>
                VehicleTypeEnum::MOTORBIKE,

            'is_verified' =>
                true,

            'rating' =>
                5.00,
        ]);

        $restaurant = $this->createRestaurant();

        /*
        |--------------------------------------------------------------------------
        | Première commande
        |--------------------------------------------------------------------------
        */

        $orderOne =
            $this->createOrder(
                $restaurant
            );

        /*
        |--------------------------------------------------------------------------
        | Deuxième commande
        |--------------------------------------------------------------------------
        */

        $orderTwo =
            $this->createOrder(
                $restaurant
            );

        /*
        |--------------------------------------------------------------------------
        | Première livraison
        |--------------------------------------------------------------------------
        */

        $deliveryOne = Delivery::create([

            'uuid' =>
                (string) Str::uuid(),

            'order_id' =>
                $orderOne->id,

            'driver_id' =>
                $driver->id,

            'status' =>
                DeliveryStatusEnum::PENDING,

            'delivery_fee' =>
                1000,

            'driver_earning' =>
                700,

            'platform_commission' =>
                300,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Deuxième livraison
        |--------------------------------------------------------------------------
        */

        $deliveryTwo = Delivery::create([

            'uuid' =>
                (string) Str::uuid(),

            'order_id' =>
                $orderTwo->id,

            'driver_id' =>
                $driver->id,

            'status' =>
                DeliveryStatusEnum::DELIVERED,

            'delivery_fee' =>
                1500,

            'driver_earning' =>
                1000,

            'platform_commission' =>
                500,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Vérification
        |--------------------------------------------------------------------------
        */

        $this->assertCount(
            2,
            $driver->deliveries
        );

        $this->assertTrue(
            $driver->deliveries
                ->contains($deliveryOne)
        );

        $this->assertTrue(
            $driver->deliveries
                ->contains($deliveryTwo)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Test 3
    |--------------------------------------------------------------------------
    |
    | Une commande possède une seule livraison.
    |
    */

    public function test_order_has_one_delivery(): void
    {
        $restaurant =
            $this->createRestaurant();

        $order =
            $this->createOrder(
                $restaurant
            );

        $delivery = Delivery::create([

            'uuid' =>
                (string) Str::uuid(),

            'order_id' =>
                $order->id,

            'driver_id' =>
                null,

            'status' =>
                DeliveryStatusEnum::PENDING,

            'delivery_fee' =>
                1000,

            'driver_earning' =>
                700,

            'platform_commission' =>
                300,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Order → Delivery
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $order->delivery->is($delivery)
        );

        /*
        |--------------------------------------------------------------------------
        | Delivery → Order
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $delivery->order->is($order)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Test 4
    |--------------------------------------------------------------------------
    |
    | Une livraison peut exister sans livreur.
    |
    | C'est essentiel pour notre futur Delivery Assignment Engine.
    |
    */

    public function test_delivery_can_exist_without_driver(): void
    {
        $restaurant =
            $this->createRestaurant();

        $order =
            $this->createOrder(
                $restaurant
            );

        $delivery = Delivery::create([

            'uuid' =>
                (string) Str::uuid(),

            'order_id' =>
                $order->id,

            'driver_id' =>
                null,

            'status' =>
                DeliveryStatusEnum::PENDING,

            'delivery_fee' =>
                1000,

            'driver_earning' =>
                700,

            'platform_commission' =>
                300,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Le livreur doit être NULL.
        |--------------------------------------------------------------------------
        */

        $this->assertNull(
            $delivery->driver
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Test 5
    |--------------------------------------------------------------------------
    |
    | Vérifie l'historique d'une livraison.
    |
    */

    public function test_delivery_has_histories(): void
    {
        $restaurant =
            $this->createRestaurant();

        $order =
            $this->createOrder(
                $restaurant
            );

        $delivery = Delivery::create([

            'uuid' =>
                (string) Str::uuid(),

            'order_id' =>
                $order->id,

            'status' =>
                DeliveryStatusEnum::PENDING,

            'delivery_fee' =>
                1000,

            'driver_earning' =>
                700,

            'platform_commission' =>
                300,
        ]);

        $history = DeliveryHistory::create([

            'delivery_id' =>
                $delivery->id,

            'status' =>
                DeliveryStatusEnum::PENDING,

            'created_by' =>
                null,

            'comment' =>
                'Livraison créée.',

            'metadata' => [

                'source' =>
                    'system',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Delivery → Histories
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $delivery->histories
                ->contains($history)
        );

        /*
        |--------------------------------------------------------------------------
        | History → Delivery
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $history->delivery->is($delivery)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Test 6
    |--------------------------------------------------------------------------
    |
    | Vérifie qu'un Driver peut enregistrer plusieurs positions GPS.
    |
    */

    public function test_driver_has_locations(): void
    {
        $user =
            User::factory()->create();

        $driver = Driver::create([

            'uuid' =>
                (string) Str::uuid(),

            'user_id' =>
                $user->id,

            'status' =>
                DriverStatusEnum::ONLINE,

            'vehicle_type' =>
                VehicleTypeEnum::MOTORBIKE,

            'is_verified' =>
                true,

            'rating' =>
                5.00,
        ]);

        $location = DriverLocation::create([

            'driver_id' =>
                $driver->id,

            'delivery_id' =>
                null,

            'latitude' =>
                6.3703,

            'longitude' =>
                2.3912,

            'accuracy' =>
                10.50,

            'speed' =>
                35.00,

            'heading' =>
                180.00,

            'recorded_at' =>
                now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Driver → Locations
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $driver->locations
                ->contains($location)
        );

        /*
        |--------------------------------------------------------------------------
        | Location → Driver
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $location->driver->is($driver)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Test 7
    |--------------------------------------------------------------------------
    |
    | Vérifie qu'une position GPS peut être associée
    | à une livraison précise.
    |
    */

    public function test_location_can_belong_to_delivery(): void
    {
        $user =
            User::factory()->create();

        $driver = Driver::create([

            'uuid' =>
                (string) Str::uuid(),

            'user_id' =>
                $user->id,

            'status' =>
                DriverStatusEnum::BUSY,

            'vehicle_type' =>
                VehicleTypeEnum::MOTORBIKE,

            'is_verified' =>
                true,

            'rating' =>
                5.00,
        ]);

        $restaurant =
            $this->createRestaurant();

        $order =
            $this->createOrder(
                $restaurant
            );

        $delivery = Delivery::create([

            'uuid' =>
                (string) Str::uuid(),

            'order_id' =>
                $order->id,

            'driver_id' =>
                $driver->id,

            'status' =>
                DeliveryStatusEnum::ON_THE_WAY,

            'delivery_fee' =>
                1000,

            'driver_earning' =>
                700,

            'platform_commission' =>
                300,
        ]);

        $location = DriverLocation::create([

            'driver_id' =>
                $driver->id,

            'delivery_id' =>
                $delivery->id,

            'latitude' =>
                6.3703,

            'longitude' =>
                2.3912,

            'accuracy' =>
                8.00,

            'speed' =>
                30.00,

            'heading' =>
                90.00,

            'recorded_at' =>
                now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Delivery → Locations
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $delivery->locations
                ->contains($location)
        );

        /*
        |--------------------------------------------------------------------------
        | Location → Delivery
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $location->delivery->is($delivery)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Test 8
    |--------------------------------------------------------------------------
    |
    | Vérifie qu'une livraison peut posséder plusieurs preuves.
    |
    */

    public function test_delivery_has_proofs(): void
    {
        $restaurant =
            $this->createRestaurant();

        $order =
            $this->createOrder(
                $restaurant
            );

        $delivery = Delivery::create([

            'uuid' =>
                (string) Str::uuid(),

            'order_id' =>
                $order->id,

            'status' =>
                DeliveryStatusEnum::DELIVERED,

            'delivery_fee' =>
                1000,

            'driver_earning' =>
                700,

            'platform_commission' =>
                300,

            'delivered_at' =>
                now(),
        ]);

        $proof = DeliveryProof::create([

            'uuid' =>
                (string) Str::uuid(),

            'delivery_id' =>
                $delivery->id,

            'type' =>
                DeliveryProofTypeEnum::CUSTOMER_CONFIRMATION,

            'value' =>
                'customer_confirmed',

            'metadata' => [

                'method' =>
                    'app',
            ],

            'created_by' =>
                null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Delivery → Proofs
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $delivery->proofs
                ->contains($proof)
        );

        /*
        |--------------------------------------------------------------------------
        | Proof → Delivery
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $proof->delivery->is($delivery)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Test 9
    |--------------------------------------------------------------------------
    |
    | Vérifie que les Enums Delivery et Driver
    | sont correctement castés par Eloquent.
    |
    */

    public function test_delivery_and_driver_enums_are_cast_correctly(): void
    {
        $user =
            User::factory()->create();

        $driver = Driver::create([

            'uuid' =>
                (string) Str::uuid(),

            'user_id' =>
                $user->id,

            'status' =>
                DriverStatusEnum::ONLINE,

            'vehicle_type' =>
                VehicleTypeEnum::MOTORBIKE,

            'is_verified' =>
                true,

            'rating' =>
                5.00,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Vérification DriverStatusEnum
        |--------------------------------------------------------------------------
        */

        $this->assertInstanceOf(
            DriverStatusEnum::class,
            $driver->status
        );

        /*
        |--------------------------------------------------------------------------
        | Vérification VehicleTypeEnum
        |--------------------------------------------------------------------------
        */

        $this->assertInstanceOf(
            VehicleTypeEnum::class,
            $driver->vehicle_type
        );

        $restaurant =
            $this->createRestaurant();

        $order =
            $this->createOrder(
                $restaurant
            );

        $delivery = Delivery::create([

            'uuid' =>
                (string) Str::uuid(),

            'order_id' =>
                $order->id,

            'status' =>
                DeliveryStatusEnum::PENDING,

            'delivery_fee' =>
                1000,

            'driver_earning' =>
                700,

            'platform_commission' =>
                300,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Vérification DeliveryStatusEnum
        |--------------------------------------------------------------------------
        */

        $this->assertInstanceOf(
            DeliveryStatusEnum::class,
            $delivery->status
        );

        $this->assertSame(
            DeliveryStatusEnum::PENDING,
            $delivery->status
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Test 10
    |--------------------------------------------------------------------------
    |
    | Vérifie que DeliveryProof utilise correctement
    | DeliveryProofTypeEnum.
    |
    */

    public function test_delivery_proof_enum_is_cast_correctly(): void
    {
        $restaurant =
            $this->createRestaurant();

        $order =
            $this->createOrder(
                $restaurant
            );

        $delivery = Delivery::create([

            'uuid' =>
                (string) Str::uuid(),

            'order_id' =>
                $order->id,

            'status' =>
                DeliveryStatusEnum::DELIVERED,

            'delivery_fee' =>
                1000,

            'driver_earning' =>
                700,

            'platform_commission' =>
                300,
        ]);

        $proof = DeliveryProof::create([

            'uuid' =>
                (string) Str::uuid(),

            'delivery_id' =>
                $delivery->id,

            'type' =>
                DeliveryProofTypeEnum::OTP,

            'value' =>
                'OTP_REFERENCE',

            'metadata' =>
                [],

            'created_by' =>
                null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Vérification du cast
        |--------------------------------------------------------------------------
        */

        $this->assertInstanceOf(
            DeliveryProofTypeEnum::class,
            $proof->type
        );

        $this->assertSame(
            DeliveryProofTypeEnum::OTP,
            $proof->type
        );
    }
}