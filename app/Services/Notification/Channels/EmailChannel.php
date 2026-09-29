<?php

namespace App\Services\Notification\Channels;

use App\Models\Notification;
use App\Models\User;

/**
 * Email notification channel.
 *
 * This channel is responsible for validating and processing
 * notifications intended to be delivered by email.
 *
 * The actual email transport/provider will be connected later.
 */
class EmailChannel implements NotificationChannelInterface
{
    /**
     * Send the notification through the email channel.
     *
     * At this stage, the channel:
     *
     * - validates that the recipient has an email address;
     * - marks the notification as sent when valid;
     * - records the failure when no email address exists.
     *
     * The actual email delivery will be integrated later
     * through Laravel's Mail system.
     */
    public function send(Notification $notification): bool
    {
        $notifiable = $notification->notifiable;

        if (!$this->hasValidEmailRecipient($notifiable)) {
            $notification->markAsFailed(
                'Le destinataire ne possède pas d\'adresse email.'
            );

            return false;
        }

        if ($notification->sent_at !== null) {
            return true;
        }

        $notification->markAsSent();

        return true;
    }

    /**
     * Determine whether the notification recipient
     * has a usable email address.
     */
    protected function hasValidEmailRecipient(
        mixed $notifiable
    ): bool {
        if (!$notifiable instanceof User) {
            return false;
        }

        return is_string($notifiable->email)
            && filter_var(
                $notifiable->email,
                FILTER_VALIDATE_EMAIL
            ) !== false;
    }
}