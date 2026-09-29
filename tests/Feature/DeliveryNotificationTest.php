<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use App\Notifications\DeliveryNotification;
use App\Services\Delivery\DeliveryNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DeliveryNotificationTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Driver assigned
    |--------------------------------------------------------------------------
    */

    public function test_driver_is_notified_when_delivery_is_assigned(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $driver = Driver::factory()->create([
            'user_id' => $user->id,
        ]);

        $order = Order::factory()->create();

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
            'driver_id' => $driver->id,
        ]);

        app(DeliveryNotificationService::class)
            ->driverAssigned($delivery);

        Notification::assertSentTo(
            $user,
            DeliveryNotification::class,
            function (
                DeliveryNotification $notification
            ) use ($delivery) {

                return $notification->toDatabase(
                    User::factory()->make()
                )->data['delivery_id'] === $delivery->id;
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Customer status changed
    |--------------------------------------------------------------------------
    */

    public function test_customer_is_notified_when_delivery_status_changes(): void
    {
        Notification::fake();

        $customer = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $customer->id,
        ]);

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
        ]);

        app(DeliveryNotificationService::class)
            ->customerStatusChanged($delivery);

        Notification::assertSentTo(
            $customer,
            DeliveryNotification::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delivery started
    |--------------------------------------------------------------------------
    */

    public function test_customer_is_notified_when_delivery_starts(): void
    {
        Notification::fake();

        $customer = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $customer->id,
        ]);

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
        ]);

        app(DeliveryNotificationService::class)
            ->deliveryStarted($delivery);

        Notification::assertSentTo(
            $customer,
            DeliveryNotification::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delivery completed
    |--------------------------------------------------------------------------
    */

    public function test_customer_is_notified_when_delivery_is_completed(): void
    {
        Notification::fake();

        $customer = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $customer->id,
        ]);

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
        ]);

        app(DeliveryNotificationService::class)
            ->deliveryCompleted($delivery);

        Notification::assertSentTo(
            $customer,
            DeliveryNotification::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delivery failed
    |--------------------------------------------------------------------------
    */

    public function test_customer_is_notified_when_delivery_fails(): void
    {
        Notification::fake();

        $customer = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $customer->id,
        ]);

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
        ]);

        app(DeliveryNotificationService::class)
            ->deliveryFailed($delivery);

        Notification::assertSentTo(
            $customer,
            DeliveryNotification::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delivery cancelled
    |--------------------------------------------------------------------------
    */

    public function test_customer_is_notified_when_delivery_is_cancelled(): void
    {
        Notification::fake();

        $customer = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $customer->id,
        ]);

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
        ]);

        app(DeliveryNotificationService::class)
            ->deliveryCancelled($delivery);

        Notification::assertSentTo(
            $customer,
            DeliveryNotification::class
        );
    }
}
