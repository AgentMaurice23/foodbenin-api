<?php

namespace Tests\Feature;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationService = app(
            NotificationService::class
        );
    }

    /**
     * A notification can be created for a user.
     */
    public function test_notification_can_be_created_for_user(): void
    {
        $user = User::factory()->create();

        $notification = $this->notificationService->create(
            notifiable: $user,
            type: NotificationTypeEnum::ORDER_CREATED,
            channel: NotificationChannelEnum::DATABASE,
            title: 'Nouvelle commande',
            message: 'Votre commande a été créée.',
            data: [
                'order_id' => 123,
            ],
        );

        $this->assertInstanceOf(
            Notification::class,
            $notification
        );

        $this->assertEquals(
            $user->id,
            $notification->notifiable_id
        );

        $this->assertEquals(
            User::class,
            $notification->notifiable_type
        );

        $this->assertEquals(
            NotificationTypeEnum::ORDER_CREATED,
            $notification->type
        );

        $this->assertEquals(
            NotificationChannelEnum::DATABASE,
            $notification->channel
        );
    }

    /**
     * A notification can be attached to an order.
     */
    public function test_notification_can_be_created_for_order(): void
    {
        $order = Order::factory()->create();

        $notification = $this->notificationService->create(
            notifiable: $order,
            type: NotificationTypeEnum::ORDER_ASSIGNED,
            channel: NotificationChannelEnum::DATABASE,
            title: 'Commande assignée',
            message: 'Votre commande a été assignée à un livreur.',
            data: [
                'order_id' => $order->id,
            ],
        );

        $this->assertEquals(
            $order->id,
            $notification->notifiable_id
        );

        $this->assertEquals(
            Order::class,
            $notification->notifiable_type
        );
    }

    /**
     * Notification data is stored correctly.
     */
    public function test_notification_data_is_stored_correctly(): void
    {
        $user = User::factory()->create();

        $data = [
            'order_id' => 123,
            'status' => 'preparing',
            'restaurant_id' => 45,
        ];

        $notification = $this->notificationService->create(
            notifiable: $user,
            type: NotificationTypeEnum::ORDER_PREPARING,
            channel: NotificationChannelEnum::DATABASE,
            title: 'Commande en préparation',
            message: 'Le restaurant prépare votre commande.',
            data: $data,
        );

        $this->assertEquals(
            $data,
            $notification->fresh()->data
        );
    }

    /**
     * A new notification is unread.
     */
    public function test_new_notification_is_unread(): void
    {
        $user = User::factory()->create();

        $notification = $this->notificationService->create(
            notifiable: $user,
            type: NotificationTypeEnum::PAYMENT_PENDING,
            channel: NotificationChannelEnum::DATABASE,
            title: 'Paiement en attente',
            message: 'Votre paiement est en attente.',
        );

        $this->assertNull(
            $notification->read_at
        );

        $this->assertFalse(
            $notification->isRead()
        );
    }

    /**
     * A notification can be marked as read.
     */
    public function test_notification_can_be_marked_as_read(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
        ]);

        $this->notificationService->markAsRead(
            $notification
        );

        $notification->refresh();

        $this->assertNotNull(
            $notification->read_at
        );

        $this->assertTrue(
            $notification->isRead()
        );
    }

    /**
     * A notification can be marked as sent.
     */
    public function test_notification_can_be_marked_as_sent(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
        ]);

        $this->notificationService->markAsSent(
            $notification
        );

        $notification->refresh();

        $this->assertNotNull(
            $notification->sent_at
        );

        $this->assertTrue(
            $notification->isSent()
        );

        $this->assertNull(
            $notification->failed_at
        );

        $this->assertNull(
            $notification->error_message
        );
    }

    /**
     * A notification can be marked as failed.
     */
    public function test_notification_can_be_marked_as_failed(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
        ]);

        $error = 'Échec de l’envoi de la notification.';

        $this->notificationService->markAsFailed(
            $notification,
            $error
        );

        $notification->refresh();

        $this->assertNotNull(
            $notification->failed_at
        );

        $this->assertEquals(
            $error,
            $notification->error_message
        );

        $this->assertTrue(
            $notification->hasFailed()
        );
    }

    /**
     * Database channel can be used.
     */
    public function test_database_channel_is_supported(): void
    {
        $this->assertEquals(
            'database',
            NotificationChannelEnum::DATABASE->value
        );
    }

    /**
     * Email channel can be used.
     */
    public function test_email_channel_is_supported(): void
    {
        $this->assertEquals(
            'email',
            NotificationChannelEnum::EMAIL->value
        );
    }

    /**
     * SMS channel can be used.
     */
    public function test_sms_channel_is_supported(): void
    {
        $this->assertEquals(
            'sms',
            NotificationChannelEnum::SMS->value
        );
    }

    /**
     * WhatsApp channel can be used.
     */
    public function test_whatsapp_channel_is_supported(): void
    {
        $this->assertEquals(
            'whatsapp',
            NotificationChannelEnum::WHATSAPP->value
        );
    }

    /**
     * Push channel can be used.
     */
    public function test_push_channel_is_supported(): void
    {
        $this->assertEquals(
            'push',
            NotificationChannelEnum::PUSH->value
        );
    }

        #[Test]
    public function notification_can_be_created_and_dispatched(): void
    {
        $user = User::factory()->create();

        $notification = app(NotificationService::class)->notify(
            $user,
            NotificationTypeEnum::ORDER_READY,
            NotificationChannelEnum::DATABASE,
            'Commande prête',
            'Votre commande est prête.',
            [
                'order_id' => 123,
            ],
        );

        $this->assertInstanceOf(
            Notification::class,
            $notification
        );

        $this->assertEquals(
            NotificationTypeEnum::ORDER_READY,
            $notification->type
        );

        $this->assertEquals(
            NotificationChannelEnum::DATABASE,
            $notification->channel
        );

        $this->assertEquals(
            [
                'order_id' => 123,
            ],
            $notification->data
        );

        $this->assertNotNull(
            $notification->sent_at
        );

        $this->assertNull(
            $notification->failed_at
        );
    }
    
    #[Test]
    public function notification_can_be_created_and_dispatched_by_sms(): void
    {
        $user = User::factory()->create([
            'phone' => '+22997000000',
        ]);

        $notification = app(NotificationService::class)->notify(
            $user,
            NotificationTypeEnum::ORDER_CREATED,
            NotificationChannelEnum::SMS,
            'Nouvelle commande',
            'Votre commande a été enregistrée.',
            [
                'order_id' => 123,
            ],
        );

        $this->assertEquals(
            NotificationChannelEnum::SMS,
            $notification->channel
        );

        $this->assertNotNull(
            $notification->sent_at
        );

        $this->assertNull(
            $notification->failed_at
        );
    }
    
    #[Test]
    public function notification_can_be_created_and_dispatched_by_whatsapp(): void
    {
        $user = User::factory()->create([
            'phone' => '+22997000000',
        ]);

        $notification = app(NotificationService::class)->notify(
            $user,
            NotificationTypeEnum::ORDER_CREATED,
            NotificationChannelEnum::WHATSAPP,
            'Nouvelle commande',
            'Votre commande a été enregistrée.',
            [
                'order_id' => 123,
            ],
        );

        $this->assertEquals(
            NotificationChannelEnum::WHATSAPP,
            $notification->channel
        );

        $this->assertNotNull(
            $notification->sent_at
        );

        $this->assertNull(
            $notification->failed_at
        );
    }
}
