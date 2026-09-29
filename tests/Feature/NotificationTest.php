<?php

namespace Tests\Feature;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A notification belongs to a user.
     */
    public function test_notification_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
        ]);

        $this->assertTrue(
            $notification->notifiable->is($user)
        );
    }

    /**
     * A notification can belong to an order.
     */
    public function test_notification_can_belong_to_order(): void
    {
        $order = Order::factory()->create();

        $notification = Notification::factory()->create([
            'notifiable_type' => Order::class,
            'notifiable_id' => $order->id,
        ]);

        $this->assertTrue(
            $notification->notifiable->is($order)
        );
    }

    /**
     * Notification type is cast to enum.
     */
    public function test_notification_type_is_cast_to_enum(): void
    {
        $notification = Notification::factory()->create([
            'type' => NotificationTypeEnum::ORDER_CREATED,
        ]);

        $this->assertInstanceOf(
            NotificationTypeEnum::class,
            $notification->type
        );

        $this->assertEquals(
            NotificationTypeEnum::ORDER_CREATED,
            $notification->type
        );
    }

    /**
     * Notification channel is cast to enum.
     */
    public function test_notification_channel_is_cast_to_enum(): void
    {
        $notification = Notification::factory()->create([
            'channel' => NotificationChannelEnum::DATABASE,
        ]);

        $this->assertInstanceOf(
            NotificationChannelEnum::class,
            $notification->channel
        );

        $this->assertEquals(
            NotificationChannelEnum::DATABASE,
            $notification->channel
        );
    }

    /**
     * Notification data is cast to array.
     */
    public function test_notification_data_is_cast_to_array(): void
    {
        $notification = Notification::factory()->create([
            'data' => [
                'order_id' => 123,
                'status' => 'pending',
            ],
        ]);

        $this->assertIsArray($notification->data);

        $this->assertEquals(
            123,
            $notification->data['order_id']
        );
    }

    /**
     * A new notification is unread.
     */
    public function test_new_notification_is_unread(): void
    {
        $notification = Notification::factory()->create([
            'read_at' => null,
        ]);

        $this->assertFalse(
            $notification->isRead()
        );
    }

    /**
     * Notification can be marked as read.
     */
    public function test_notification_can_be_marked_as_read(): void
    {
        $notification = Notification::factory()->create([
            'read_at' => null,
        ]);

        $notification->markAsRead();

        $notification->refresh();

        $this->assertTrue(
            $notification->isRead()
        );

        $this->assertNotNull(
            $notification->read_at
        );
    }

    /**
     * Notification can be marked as sent.
     */
    public function test_notification_can_be_marked_as_sent(): void
    {
        $notification = Notification::factory()->create([
            'sent_at' => null,
            'failed_at' => now(),
            'error_message' => 'Previous error',
        ]);

        $notification->markAsSent();

        $notification->refresh();

        $this->assertTrue(
            $notification->isSent()
        );

        $this->assertNotNull(
            $notification->sent_at
        );

        $this->assertNull(
            $notification->failed_at
        );

        $this->assertNull(
            $notification->error_message
        );
    }

    /**
     * Notification can be marked as failed.
     */
    public function test_notification_can_be_marked_as_failed(): void
    {
        $notification = Notification::factory()->create([
            'failed_at' => null,
            'error_message' => null,
        ]);

        $notification->markAsFailed(
            'Unable to send notification.'
        );

        $notification->refresh();

        $this->assertTrue(
            $notification->hasFailed()
        );

        $this->assertNotNull(
            $notification->failed_at
        );

        $this->assertEquals(
            'Unable to send notification.',
            $notification->error_message
        );
    }
}