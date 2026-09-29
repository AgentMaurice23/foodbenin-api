<?php

namespace Tests\Feature\NotificationChannels;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Notification;
use App\Models\User;
use App\Services\Notification\Channels\PushChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushChannelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Push channel can send a notification.
     */
    public function test_push_channel_can_send_notification(): void
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

        app(PushChannel::class)->send($notification);

        $notification->refresh();

        $this->assertNotNull($notification->sent_at);
        $this->assertNull($notification->failed_at);
        $this->assertNull($notification->error_message);
    }

    /**
     * Push channel requires a valid notifiable model.
     */
    public function test_push_channel_requires_a_notifiable(): void
    {
        $notification = Notification::factory()->make([
            'notifiable_type' => null,
            'notifiable_id' => null,
            'channel' => NotificationChannelEnum::PUSH,
        ]);

        $this->expectException(\RuntimeException::class);

        app(PushChannel::class)->send($notification);
    }

    /**
     * Push channel does not duplicate send state.
     */
    public function test_push_channel_does_not_duplicate_send_state(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'channel' => NotificationChannelEnum::PUSH,
            'sent_at' => null,
            'failed_at' => null,
            'error_message' => null,
        ]);

        $channel = app(PushChannel::class);

        $channel->send($notification);

        $notification->refresh();

        $firstSentAt = $notification->sent_at;

        $channel->send($notification);

        $notification->refresh();

        $this->assertNotNull($notification->sent_at);
        $this->assertNull($notification->failed_at);
        $this->assertNull($notification->error_message);
        $this->assertEquals(
            $firstSentAt->toDateTimeString(),
            $notification->sent_at->toDateTimeString()
        );
    }
}