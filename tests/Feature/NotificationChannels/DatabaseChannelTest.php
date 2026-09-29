<?php

namespace Tests\Feature\NotificationChannels;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Notification;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use App\Services\Notification\Channels\DatabaseChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseChannelTest extends TestCase
{
    use RefreshDatabase;

    protected DatabaseChannel $channel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->channel = app(DatabaseChannel::class);
    }

    #[Test]
    public function database_channel_can_send_notification(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => NotificationTypeEnum::ORDER_CREATED,
            'channel' => NotificationChannelEnum::DATABASE,
            'sent_at' => null,
        ]);

        $result = $this->channel->send($notification);

        $this->assertTrue($result);

        $this->assertNotNull(
            $notification->fresh()->sent_at
        );
    }

    #[Test]
    public function database_channel_clears_previous_failure_when_sending(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => NotificationTypeEnum::ORDER_CREATED,
            'channel' => NotificationChannelEnum::DATABASE,
            'sent_at' => null,
            'failed_at' => now(),
            'error_message' => 'Previous failure',
        ]);

        $result = $this->channel->send($notification);

        $freshNotification = $notification->fresh();

        $this->assertTrue($result);

        $this->assertNotNull(
            $freshNotification->sent_at
        );

        $this->assertNull(
            $freshNotification->failed_at
        );

        $this->assertNull(
            $freshNotification->error_message
        );
    }

    #[Test]
    public function database_channel_does_not_duplicate_send_state(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => NotificationTypeEnum::ORDER_CREATED,
            'channel' => NotificationChannelEnum::DATABASE,
            'sent_at' => now(),
        ]);

        $originalSentAt = $notification->sent_at;

        $result = $this->channel->send($notification);

        $freshNotification = $notification->fresh();

        $this->assertTrue($result);

        $this->assertNotNull($freshNotification->sent_at);

        $this->assertEquals(
            $originalSentAt->timestamp,
            $freshNotification->sent_at->timestamp
        );
    }
}