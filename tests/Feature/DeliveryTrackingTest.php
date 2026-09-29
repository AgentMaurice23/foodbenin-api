<?php

namespace Tests\Feature;

use App\Enums\DeliveryStatusEnum;
use App\Enums\DriverStatusEnum;
use App\Models\Address;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Order;
use App\Services\Delivery\TrackingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class DeliveryTrackingTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Création d'un livreur
    |--------------------------------------------------------------------------
    */

    private function createDriver(): Driver
    {
        return Driver::factory()->create([
            'status' => DriverStatusEnum::ONLINE,
            'is_verified' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Création d'une livraison
    |--------------------------------------------------------------------------
    */

    private function createDelivery(
        Driver $driver,
        DeliveryStatusEnum $status = DeliveryStatusEnum::ACCEPTED
    ): Delivery {

        $order = Order::factory()->create([
            'address_id' => Address::factory(),
        ]);

        return Delivery::factory()->create([
            'order_id' => $order->id,
            'driver_id' => $driver->id,
            'status' => $status,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Un livreur peut enregistrer une position
    |--------------------------------------------------------------------------
    */

    public function test_driver_can_record_location(): void
    {
        $driver = $this->createDriver();

        $service = app(TrackingService::class);

        $location = $service->recordLocation(
            driver: $driver,
            latitude: 6.3703,
            longitude: 2.3912,
            accuracy: 5.5,
            speed: 32.4,
            heading: 180.0,
        );

        $this->assertDatabaseHas('driver_locations', [
            'id' => $location->id,
            'driver_id' => $driver->id,
            'delivery_id' => null,
            'latitude' => '6.3703000',
            'longitude' => '2.3912000',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Une position peut être associée à une livraison
    |--------------------------------------------------------------------------
    */

    public function test_driver_can_record_location_for_his_delivery(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::ACCEPTED
        );

        $service = app(TrackingService::class);

        $location = $service->recordLocation(
            driver: $driver,
            latitude: 6.3703,
            longitude: 2.3912,
            accuracy: 4.2,
            speed: 28.5,
            heading: 90.0,
            delivery: $delivery,
        );

        $this->assertDatabaseHas('driver_locations', [
            'id' => $location->id,
            'driver_id' => $driver->id,
            'delivery_id' => $delivery->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Un autre livreur ne peut pas tracker la livraison
    |--------------------------------------------------------------------------
    */

    public function test_driver_cannot_record_location_for_another_driver_delivery(): void
    {
        $driver = $this->createDriver();

        $anotherDriver = $this->createDriver();

        $delivery = $this->createDelivery(
            $anotherDriver,
            DeliveryStatusEnum::ACCEPTED
        );

        $service = app(TrackingService::class);

        $this->expectException(RuntimeException::class);

        $service->recordLocation(
            driver: $driver,
            latitude: 6.3703,
            longitude: 2.3912,
            delivery: $delivery,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Une livraison terminée ne peut plus être trackée
    |--------------------------------------------------------------------------
    */

    public function test_final_delivery_cannot_be_tracked(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::DELIVERED
        );

        $service = app(TrackingService::class);

        $this->expectException(RuntimeException::class);

        $service->recordLocation(
            driver: $driver,
            latitude: 6.3703,
            longitude: 2.3912,
            delivery: $delivery,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Une livraison PENDING ne peut pas être trackée
    |--------------------------------------------------------------------------
    */

    public function test_pending_delivery_cannot_be_tracked(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::PENDING
        );

        $service = app(TrackingService::class);

        $this->expectException(RuntimeException::class);

        $service->recordLocation(
            driver: $driver,
            latitude: 6.3703,
            longitude: 2.3912,
            delivery: $delivery,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Latitude invalide
    |--------------------------------------------------------------------------
    */

    public function test_invalid_latitude_is_rejected(): void
    {
        $driver = $this->createDriver();

        $service = app(TrackingService::class);

        $this->expectException(RuntimeException::class);

        $service->recordLocation(
            driver: $driver,
            latitude: 100,
            longitude: 2.3912,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Longitude invalide
    |--------------------------------------------------------------------------
    */

    public function test_invalid_longitude_is_rejected(): void
    {
        $driver = $this->createDriver();

        $service = app(TrackingService::class);

        $this->expectException(RuntimeException::class);

        $service->recordLocation(
            driver: $driver,
            latitude: 6.3703,
            longitude: 200,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Heading invalide
    |--------------------------------------------------------------------------
    */

    public function test_invalid_heading_is_rejected(): void
    {
        $driver = $this->createDriver();

        $service = app(TrackingService::class);

        $this->expectException(RuntimeException::class);

        $service->recordLocation(
            driver: $driver,
            latitude: 6.3703,
            longitude: 2.3912,
            heading: 360,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Récupérer la dernière position du livreur
    |--------------------------------------------------------------------------
    */

    public function test_can_get_latest_driver_location(): void
    {
        $driver = $this->createDriver();

        $service = app(TrackingService::class);

        $oldLocation = $service->recordLocation(
            driver: $driver,
            latitude: 6.3500,
            longitude: 2.3800,
            recordedAt: now()->subMinutes(5),
        );

        $latestLocation = $service->recordLocation(
            driver: $driver,
            latitude: 6.3703,
            longitude: 2.3912,
            recordedAt: now(),
        );

        $result = $service->getLatestDriverLocation(
            $driver
        );

        $this->assertNotNull($result);

        $this->assertSame(
            $latestLocation->id,
            $result->id
        );

        $this->assertNotSame(
            $oldLocation->id,
            $result->id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Récupérer la dernière position d'une livraison
    |--------------------------------------------------------------------------
    */

    public function test_can_get_latest_delivery_location(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::ON_THE_WAY
        );

        $service = app(TrackingService::class);

        $service->recordLocation(
            driver: $driver,
            latitude: 6.3500,
            longitude: 2.3800,
            delivery: $delivery,
            recordedAt: now()->subMinutes(5),
        );

        $latestLocation = $service->recordLocation(
            driver: $driver,
            latitude: 6.3703,
            longitude: 2.3912,
            delivery: $delivery,
            recordedAt: now(),
        );

        $result = $service->getLatestDeliveryLocation(
            $delivery
        );

        $this->assertNotNull($result);

        $this->assertSame(
            $latestLocation->id,
            $result->id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Récupérer l'historique GPS
    |--------------------------------------------------------------------------
    */

    public function test_can_get_delivery_locations_history(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::ON_THE_WAY
        );

        $service = app(TrackingService::class);

        $service->recordLocation(
            driver: $driver,
            latitude: 6.3500,
            longitude: 2.3800,
            delivery: $delivery,
            recordedAt: now()->subMinutes(10),
        );

        $service->recordLocation(
            driver: $driver,
            latitude: 6.3600,
            longitude: 2.3850,
            delivery: $delivery,
            recordedAt: now()->subMinutes(5),
        );

        $service->recordLocation(
            driver: $driver,
            latitude: 6.3703,
            longitude: 2.3912,
            delivery: $delivery,
            recordedAt: now(),
        );

        $locations = $service->getDeliveryLocations(
            $delivery
        );

        $this->assertCount(
            3,
            $locations
        );

        $this->assertTrue(
            $locations->first()->recorded_at
                <= $locations->last()->recorded_at
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Limiter les positions d'un livreur
    |--------------------------------------------------------------------------
    */

    public function test_can_get_driver_locations_with_limit(): void
    {
        $driver = $this->createDriver();

        $service = app(TrackingService::class);

        for ($i = 1; $i <= 5; $i++) {
            $service->recordLocation(
                driver: $driver,
                latitude: 6.3500 + ($i / 1000),
                longitude: 2.3800 + ($i / 1000),
                recordedAt: now()->subMinutes(10 - $i),
            );
        }

        $locations = $service->getDriverLocations(
            $driver,
            3
        );

        $this->assertCount(
            3,
            $locations
        );
    }
}
