<?php

namespace App\Services\Notification\Channels;

use App\Models\Notification;

/**
 * Database notification channel.
 *
 * This channel handles notifications that are persisted
 * directly in the application's database.
 */
class DatabaseChannel implements NotificationChannelInterface
{
    /**
     * Send the notification through the database channel.
     *
     * A notification is considered successfully sent when
     * its sent_at timestamp is recorded.
     *
     * If the notification has already been sent, its original
     * sent_at timestamp is preserved to avoid duplicating
     * the send state.
     */
    public function send(Notification $notification): bool
    {
        if ($notification->sent_at !== null) {
            return true;
        }

        $notification->markAsSent();

        return true;
    }
}