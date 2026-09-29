<?php

namespace App\Services\Notification;

use App\Enums\NotificationChannelEnum;
use App\Models\Notification;
use App\Services\Notification\Channels\DatabaseChannel;
use App\Services\Notification\Channels\EmailChannel;
use App\Services\Notification\Channels\NotificationChannelInterface;
use App\Services\Notification\Channels\PushChannel;
use App\Services\Notification\Channels\SmsChannel;
use App\Services\Notification\Channels\WhatsappChannel;
use InvalidArgumentException;

/**
 * Dispatches notifications to their configured delivery channel.
 *
 * The dispatcher is responsible for resolving the appropriate
 * notification channel and delegating the actual delivery.
 */
class NotificationDispatcher
{
    /**
     * Dispatch a notification through its configured channel.
     */
    public function dispatch(Notification $notification): bool
    {
        $channel = $this->resolveChannel(
            $notification->channel
        );

        return $channel->send($notification);
    }

    /**
     * Resolve the notification channel implementation.
     */
    public function resolveChannel(
        NotificationChannelEnum $channel
    ): NotificationChannelInterface {
        return match ($channel) {
            NotificationChannelEnum::DATABASE =>
                app(DatabaseChannel::class),

            NotificationChannelEnum::EMAIL =>
                app(EmailChannel::class),

            NotificationChannelEnum::SMS =>
                app(SmsChannel::class),

            NotificationChannelEnum::WHATSAPP =>
                app(WhatsappChannel::class),

            NotificationChannelEnum::PUSH =>
                app(PushChannel::class),

            default => throw new InvalidArgumentException(
                "Canal de notification non supporté : {$channel->value}"
            ),
        };
    }
}