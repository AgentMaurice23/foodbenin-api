<?php

namespace Tests\Feature;

use App\Enums\DeliveryStatusEnum;
use App\Enums\DriverStatusEnum;
use App\Enums\VehicleTypeEnum;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\User;
use App\Services\Delivery\DeliveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class DeliveryAssignmentTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Création d'un Driver vérifié et disponible
    |--------------------------------------------------------------------------
    |
    | Méthode utilitaire utilisée par plusieurs tests.
    |
    */

    private function createOnlineDriver(): Driver
    {
        $user = User::factory()->create();

        return Driver::create([

            'user_id' => $user->id,

            'status' =>
                DriverStatusEnum::ONLINE,

            'vehicle_type' =>
                VehicleTypeEnum::MOTORBIKE,

            'is_verified' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Création d'une Delivery en attente
    |--------------------------------------------------------------------------
    |
    | Cette méthode sera adaptée à tes factories si nécessaire.
    |
    */

    private function createPendingDelivery(): Delivery
    {
        return Delivery::factory()->create([

            'status' =>
                DeliveryStatusEnum::PENDING,

            'driver_id' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Assignation réussie
    |--------------------------------------------------------------------------
    |
    | Vérifie le scénario principal :
    |
    | Delivery PENDING
    | +
    | Driver ONLINE + VERIFIED
    | ↓
    | Delivery ASSIGNED
    | Driver BUSY
    |
    */

    public function test_online_verified_driver_can_be_assigned(): void
    {
        $driver =
            $this->createOnlineDriver();

        $delivery =
            $this->createPendingDelivery();

        $service =
            app(DeliveryService::class);

        $result =
            $service->assignDriver(
                $delivery,
                $driver
            );

        $result->refresh();
        $driver->refresh();

        $this->assertSame(
            $driver->id,
            $result->driver_id
        );

        $this->assertSame(
            DeliveryStatusEnum::ASSIGNED,
            $result->status
        );

        $this->assertSame(
            DriverStatusEnum::BUSY,
            $driver->status
        );

        $this->assertNotNull(
            $result->assigned_at
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Refuser un Driver OFFLINE
    |--------------------------------------------------------------------------
    */

    public function test_offline_driver_cannot_be_assigned(): void
    {
        $driver =
            $this->createOnlineDriver();

        $driver->update([
            'status' =>
                DriverStatusEnum::OFFLINE,
        ]);

        $delivery =
            $this->createPendingDelivery();

        $service =
            app(DeliveryService::class);

        $this->expectException(
            RuntimeException::class
        );

        $service->assignDriver(
            $delivery,
            $driver
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Refuser un Driver BUSY
    |--------------------------------------------------------------------------
    */

    public function test_busy_driver_cannot_be_assigned(): void
    {
        $driver =
            $this->createOnlineDriver();

        $driver->update([
            'status' =>
                DriverStatusEnum::BUSY,
        ]);

        $delivery =
            $this->createPendingDelivery();

        $service =
            app(DeliveryService::class);

        $this->expectException(
            RuntimeException::class
        );

        $service->assignDriver(
            $delivery,
            $driver
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Refuser un Driver SUSPENDED
    |--------------------------------------------------------------------------
    */

    public function test_suspended_driver_cannot_be_assigned(): void
    {
        $driver =
            $this->createOnlineDriver();

        $driver->update([
            'status' =>
                DriverStatusEnum::SUSPENDED,
        ]);

        $delivery =
            $this->createPendingDelivery();

        $service =
            app(DeliveryService::class);

        $this->expectException(
            RuntimeException::class
        );

        $service->assignDriver(
            $delivery,
            $driver
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Refuser un Driver non vérifié
    |--------------------------------------------------------------------------
    */

    public function test_unverified_driver_cannot_be_assigned(): void
    {
        $driver =
            $this->createOnlineDriver();

        $driver->update([
            'is_verified' => false,
        ]);

        $delivery =
            $this->createPendingDelivery();

        $service =
            app(DeliveryService::class);

        $this->expectException(
            RuntimeException::class
        );

        $service->assignDriver(
            $delivery,
            $driver
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Refuser une Delivery déjà assignée
    |--------------------------------------------------------------------------
    */

    public function test_already_assigned_delivery_cannot_be_assigned_again(): void
    {
        $driver =
            $this->createOnlineDriver();

        $delivery =
            $this->createPendingDelivery();

        $service =
            app(DeliveryService::class);

        $service->assignDriver(
            $delivery,
            $driver
        );

        $anotherDriver =
            $this->createOnlineDriver();

        $delivery->refresh();

        $this->expectException(
            RuntimeException::class
        );

        $service->assignDriver(
            $delivery,
            $anotherDriver
        );
    }
}