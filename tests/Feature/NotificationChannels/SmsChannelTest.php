<?php

namespace Tests\Feature\NotificationChannels;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Notification;
use App\Models\User;
use App\Services\Notification\Channels\SmsChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SmsChannelTest extends TestCase
{
    use RefreshDatabase;

    protected SmsChannel $channel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->channel = app(SmsChannel::class);
    }

    #[Test]
    public function sms_channel_can_send_notification(): void
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
        ]);

        $result = $this->channel->send($notification);

        $this->assertTrue($result);

        $this->assertNotNull(
            $notification->fresh()->sent_at
        );
    }

    #[Test]
    public function sms_channel_requires_a_phone_number(): void
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
        ]);

        $notification->setRelation(
            'notifiable',
            new User([
                'phone' => null,
            ])
        );

        $result = $this->channel->send($notification);

        $this->assertFalse($result);

        $freshNotification = $notification->fresh();

        $this->assertNull(
            $freshNotification->sent_at
        );

        $this->assertNotNull(
            $freshNotification->failed_at
        );

        $this->assertNotNull(
            $freshNotification->error_message
        );
    }

    #[Test]
    public function sms_channel_does_not_duplicate_send_state(): void
    {
        $user = User::factory()->create([
            'phone' => '+22997000000',
        ]);

        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => NotificationTypeEnum::ORDER_CREATED,
            'channel' => NotificationChannelEnum::SMS,
            'sent_at' => now(),
        ]);

        $originalSentAt = $notification->sent_at;

        $result = $this->channel->send($notification);

        $freshNotification = $notification->fresh();

        $this->assertTrue($result);

        $this->assertEquals(
            $originalSentAt->timestamp,
            $freshNotification->sent_at->timestamp
        );
    }
}