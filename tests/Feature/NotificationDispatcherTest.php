<?php

namespace Tests\Feature;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Notification;
use App\Models\User;
use App\Services\Notification\NotificationDispatcher;
use App\Services\Notification\Channels\DatabaseChannel;
use App\Services\Notification\Channels\EmailChannel;
use App\Services\Notification\Channels\PushChannel;
use App\Services\Notification\Channels\SmsChannel;
use App\Services\Notification\Channels\WhatsappChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationDispatcherTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Dispatcher resolves database channel.
     */
    public function test_dispatcher_resolves_database_channel(): void
    {
        $dispatcher = app(NotificationDispatcher::class);

        $channel = $dispatcher->resolveChannel(
            NotificationChannelEnum::DATABASE
        );

        $this->assertInstanceOf(
            DatabaseChannel::class,
            $channel
        );
    }

    /**
     * Dispatcher resolves email channel.
     */
    public function test_dispatcher_resolves_email_channel(): void
    {
        $dispatcher = app(NotificationDispatcher::class);

        $channel = $dispatcher->resolveChannel(
            NotificationChannelEnum::EMAIL
        );

        $this->assertInstanceOf(
            EmailChannel::class,
            $channel
        );
    }

    /**
     * Dispatcher resolves SMS channel.
     */
    public function test_dispatcher_resolves_sms_channel(): void
    {
        $dispatcher = app(NotificationDispatcher::class);

        $channel = $dispatcher->resolveChannel(
            NotificationChannelEnum::SMS
        );

        $this->assertInstanceOf(
            SmsChannel::class,
            $channel
        );
    }

    /**
     * Dispatcher resolves WhatsApp channel.
     */
    public function test_dispatcher_resolves_whatsapp_channel(): void
    {
        $dispatcher = app(NotificationDispatcher::class);

        $channel = $dispatcher->resolveChannel(
            NotificationChannelEnum::WHATSAPP
        );

        $this->assertInstanceOf(
            WhatsappChannel::class,
            $channel
        );
    }

    /**
     * Dispatcher resolves push channel.
     */
    public function test_dispatcher_resolves_push_channel(): void
    {
        $dispatcher = app(NotificationDispatcher::class);

        $channel = $dispatcher->resolveChannel(
            NotificationChannelEnum::PUSH
        );

        $this->assertInstanceOf(
            PushChannel::class,
            $channel
        );
    }

    /**
     * Dispatcher sends a database notification.
     */
    public function test_dispatcher_sends_database_notification(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => NotificationTypeEnum::ORDER_CREATED,
            'channel' => NotificationChannelEnum::DATABASE,
            'sent_at' => null,
            'failed_at' => null,
            'error_message' => null,
        ]);

        $result = app(NotificationDispatcher::class)
            ->dispatch($notification);

        $notification->refresh();

        $this->assertTrue($result);
        $this->assertNotNull($notification->sent_at);
        $this->assertNull($notification->failed_at);
        $this->assertNull($notification->error_message);
    }

    /**
     * Dispatcher sends an email notification.
     */
    public function test_dispatcher_sends_email_notification(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => NotificationTypeEnum::ORDER_CREATED,
            'channel' => NotificationChannelEnum::EMAIL,
            'sent_at' => null,
            'failed_at' => null,
            'error_message' => null,
        ]);

        $result = app(NotificationDispatcher::class)
            ->dispatch($notification);

        $notification->refresh();

        $this->assertTrue($result);
        $this->assertNotNull($notification->sent_at);
    }

    /**
     * Dispatcher sends an SMS notification.
     */
    public function test_dispatcher_sends_sms_notification(): void
    {
        $user = User::factory()->create([
            'phone' => '+22997000000',
        ]);

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => NotificationTypeEnum::ORDER_CREATED,
            'channel' => NotificationChannelEnum::SMS,
            'sent_at' => null,
            'failed_at' => null,
            'error_message' => null,
        ]);

        $result = app(NotificationDispatcher::class)
            ->dispatch($notification);

        $notification->refresh();

        $this->assertTrue($result);
        $this->assertNotNull($notification->sent_at);
        $this->assertNull($notification->failed_at);
        $this->assertNull($notification->error_message);
    }
    /**
     * Dispatcher sends a WhatsApp notification.
     */
    public function test_dispatcher_sends_whatsapp_notification(): void
    {
        $user = User::factory()->create([
            'phone' => '+22997000000',
        ]);

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => NotificationTypeEnum::ORDER_CREATED,
            'channel' => NotificationChannelEnum::WHATSAPP,
            'sent_at' => null,
            'failed_at' => null,
            'error_message' => null,
        ]);

        $result = app(NotificationDispatcher::class)
            ->dispatch($notification);

        $notification->refresh();

        $this->assertTrue($result);
        $this->assertNotNull($notification->sent_at);
        $this->assertNull($notification->failed_at);
        $this->assertNull($notification->error_message);
    }

    /**
     * Dispatcher sends a push notification.
     */
    public function test_dispatcher_sends_push_notification(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => NotificationTypeEnum::ORDER_CREATED,
            'channel' => NotificationChannelEnum::PUSH,
            'sent_at' => null,
            'failed_at' => null,
            'error_message' => null,
        ]);

        $result = app(NotificationDispatcher::class)
            ->dispatch($notification);

        $notification->refresh();

        $this->assertTrue($result);
        $this->assertNotNull($notification->sent_at);
    }
}