<?php

namespace Tests\Feature;

use App\Enums\DeliveryStatusEnum;
use App\Enums\DriverStatusEnum;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use App\Services\Delivery\DeliveryWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class DeliveryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function createDriver(
        DriverStatusEnum $status = DriverStatusEnum::BUSY,
        bool $verified = true
    ): Driver {

        $user = User::factory()->create();

        return Driver::factory()->create([
            'user_id' => $user->id,
            'status' => $status,
            'is_verified' => $verified,
        ]);
    }

    protected function createDelivery(
        Driver $driver,
        DeliveryStatusEnum $status = DeliveryStatusEnum::ASSIGNED
    ): Delivery {

        $order = Order::factory()->create();

        return Delivery::factory()->create([
            'order_id' => $order->id,
            'driver_id' => $driver->id,
            'status' => $status,
            'assigned_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Acceptation
    |--------------------------------------------------------------------------
    */

    public function test_driver_can_accept_assigned_delivery(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::ASSIGNED
        );

        $service = app(DeliveryWorkflowService::class);

        $result = $service->accept($delivery);

        $this->assertSame(
            DeliveryStatusEnum::ACCEPTED,
            $result->status
        );

        $this->assertNotNull(
            $result->accepted_at
        );

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => DeliveryStatusEnum::ACCEPTED->value,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Refus
    |--------------------------------------------------------------------------
    */

    public function test_driver_can_reject_assigned_delivery(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::ASSIGNED
        );

        $service = app(DeliveryWorkflowService::class);

        $result = $service->reject(
            $delivery,
            'Trop loin de ma position.'
        );

        $this->assertSame(
            DeliveryStatusEnum::PENDING,
            $result->status
        );

        $this->assertNull(
            $result->driver_id
        );

        $this->assertNull(
            $result->assigned_at
        );

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'status' => DriverStatusEnum::ONLINE->value,
        ]);

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => DeliveryStatusEnum::PENDING->value,
            'driver_id' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Impossible d'accepter une livraison non assignée
    |--------------------------------------------------------------------------
    */

    public function test_pending_delivery_cannot_be_accepted(): void
    {
        $driver = $this->createDriver(
            DriverStatusEnum::ONLINE
        );

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::PENDING
        );

        $service = app(DeliveryWorkflowService::class);

        $this->expectException(RuntimeException::class);

        $service->accept($delivery);
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow vers le restaurant
    |--------------------------------------------------------------------------
    */

    public function test_accepted_delivery_can_start_trip_to_restaurant(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::ACCEPTED
        );

        $service = app(DeliveryWorkflowService::class);

        $result = $service->goToRestaurant($delivery);

        $this->assertSame(
            DeliveryStatusEnum::GOING_TO_RESTAURANT,
            $result->status
        );

        $this->assertNotNull(
            $result->going_to_restaurant_at
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Arrivée restaurant
    |--------------------------------------------------------------------------
    */

    public function test_driver_can_arrive_at_restaurant(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::GOING_TO_RESTAURANT
        );

        $service = app(DeliveryWorkflowService::class);

        $result = $service->arriveAtRestaurant($delivery);

        $this->assertSame(
            DeliveryStatusEnum::AT_RESTAURANT,
            $result->status
        );

        $this->assertNotNull(
            $result->arrived_restaurant_at
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Récupération commande
    |--------------------------------------------------------------------------
    */

    public function test_driver_can_pick_up_order(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::AT_RESTAURANT
        );

        $service = app(DeliveryWorkflowService::class);

        $result = $service->pickup($delivery);

        $this->assertSame(
            DeliveryStatusEnum::PICKED_UP,
            $result->status
        );

        $this->assertNotNull(
            $result->picked_up_at
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Début livraison
    |--------------------------------------------------------------------------
    */

    public function test_driver_can_start_delivery(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::PICKED_UP
        );

        $service = app(DeliveryWorkflowService::class);

        $result = $service->startDelivery($delivery);

        $this->assertSame(
            DeliveryStatusEnum::ON_THE_WAY,
            $result->status
        );

        $this->assertNotNull(
            $result->on_the_way_at
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Arrivée client
    |--------------------------------------------------------------------------
    */

    public function test_driver_can_arrive_at_customer(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::ON_THE_WAY
        );

        $service = app(DeliveryWorkflowService::class);

        $result = $service->arrive($delivery);

        $this->assertSame(
            DeliveryStatusEnum::ARRIVED,
            $result->status
        );

        $this->assertNotNull(
            $result->arrived_at
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Livraison terminée
    |--------------------------------------------------------------------------
    */

    public function test_driver_can_complete_delivery(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::ARRIVED
        );

        $service = app(DeliveryWorkflowService::class);

        $result = $service->complete($delivery);

        $this->assertSame(
            DeliveryStatusEnum::DELIVERED,
            $result->status
        );

        $this->assertNotNull(
            $result->delivered_at
        );

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'status' => DriverStatusEnum::ONLINE->value,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Échec
    |--------------------------------------------------------------------------
    */

    public function test_delivery_can_be_marked_as_failed(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::ON_THE_WAY
        );

        $service = app(DeliveryWorkflowService::class);

        $result = $service->fail(
            $delivery,
            'Client injoignable.'
        );

        $this->assertSame(
            DeliveryStatusEnum::FAILED,
            $result->status
        );

        $this->assertSame(
            'Client injoignable.',
            $result->failure_reason
        );

        $this->assertNotNull(
            $result->failed_at
        );

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'status' => DriverStatusEnum::ONLINE->value,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Annulation
    |--------------------------------------------------------------------------
    */

    public function test_delivery_can_be_cancelled(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::ACCEPTED
        );

        $service = app(DeliveryWorkflowService::class);

        $result = $service->cancel(
            $delivery,
            'Commande annulée.'
        );

        $this->assertSame(
            DeliveryStatusEnum::CANCELLED,
            $result->status
        );

        $this->assertSame(
            'Commande annulée.',
            $result->cancel_reason
        );

        $this->assertNotNull(
            $result->cancelled_at
        );

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'status' => DriverStatusEnum::ONLINE->value,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | États finaux
    |--------------------------------------------------------------------------
    */

    public function test_delivered_delivery_cannot_continue(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::DELIVERED
        );

        $service = app(DeliveryWorkflowService::class);

        $this->expectException(RuntimeException::class);

        $service->startDelivery($delivery);
    }

    public function test_cancelled_delivery_cannot_continue(): void
    {
        $driver = $this->createDriver();

        $delivery = $this->createDelivery(
            $driver,
            DeliveryStatusEnum::CANCELLED
        );

        $service = app(DeliveryWorkflowService::class);

        $this->expectException(RuntimeException::class);

        $service->startDelivery($delivery);
    }
}
