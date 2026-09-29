<?php

namespace Tests\Unit;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Notification;
use App\Models\User;
use App\Services\Notification\NotificationDispatcher;
use App\Services\Notification\NotificationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class NotificationManagerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The manager creates a notification from a template.
     */
    public function test_manager_creates_notification_from_template(): void
    {
        $user = User::factory()->create();

        $manager = app(NotificationManager::class);

        $notification = $manager->send(
            $user,
            NotificationTypeEnum::ORDER_CREATED,
            NotificationChannelEnum::DATABASE,
            [
                'order_number' => 'FB-1001',
            ]
        );

        $this->assertInstanceOf(
            Notification::class,
            $notification
        );

        $this->assertEquals(
            NotificationTypeEnum::ORDER_CREATED,
            $notification->type
        );

        $this->assertEquals(
            NotificationChannelEnum::DATABASE,
            $notification->channel
        );

        $this->assertEquals(
            'Commande reçue',
            $notification->title
        );

        $this->assertStringContainsString(
            'FB-1001',
            $notification->message
        );
    }

    /**
     * The manager persists template data.
     */
    public function test_manager_persists_template_data(): void
    {
        $user = User::factory()->create();

        $manager = app(NotificationManager::class);

        $notification = $manager->send(
            $user,
            NotificationTypeEnum::ORDER_CREATED,
            NotificationChannelEnum::DATABASE,
            [
                'order_number' => 'FB-2001',
                'restaurant_name' => 'Restaurant Test',
            ]
        );

        $this->assertEquals(
            'FB-2001',
            $notification->data['order_number']
        );

        $this->assertEquals(
            'Restaurant Test',
            $notification->data['restaurant_name']
        );
    }

    /**
     * The manager dispatches the created notification.
     */
    public function test_manager_dispatches_notification(): void
    {
        $user = User::factory()->create();

        $dispatcher = Mockery::mock(
            NotificationDispatcher::class
        );

        $dispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->andReturn(true);

        $this->app->instance(
            NotificationDispatcher::class,
            $dispatcher
        );

        $manager = app(NotificationManager::class);

        $notification = $manager->send(
            $user,
            NotificationTypeEnum::ORDER_CREATED,
            NotificationChannelEnum::DATABASE,
            [
                'order_number' => 'FB-3001',
            ]
        );

        $this->assertInstanceOf(
            Notification::class,
            $notification
        );
    }

    /**
     * The manager does not send an unsaved notification.
     */
    public function test_manager_requires_existing_notifiable(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = new User([
            'name' => 'Unsaved User',
        ]);

        app(NotificationManager::class)->send(
            $user,
            NotificationTypeEnum::ORDER_CREATED,
            NotificationChannelEnum::DATABASE,
            [
                'order_number' => 'FB-4001',
            ]
        );
    }

    /**
     * The manager supports payment failure templates.
     */
    public function test_manager_supports_payment_failed_template(): void
    {
        $user = User::factory()->create();

        $notification = app(NotificationManager::class)->send(
            $user,
            NotificationTypeEnum::PAYMENT_FAILED,
            NotificationChannelEnum::DATABASE,
            [
                'order_number' => 'FB-5001',
                'reason' => 'Solde insuffisant',
            ]
        );

        $this->assertEquals(
            'Paiement échoué',
            $notification->title
        );

        $this->assertStringContainsString(
            'FB-5001',
            $notification->message
        );

        $this->assertStringContainsString(
            'Solde insuffisant',
            $notification->message
        );
    }
}