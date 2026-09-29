<?php

namespace App\Services\Notification\Channels;

use App\Models\Notification;
use RuntimeException;

class SmsChannel implements NotificationChannelInterface
{
    /**
     * Send a notification through SMS.
     */
    public function send(Notification $notification): bool
    {
        // An already sent notification is idempotent.
        if ($notification->sent_at !== null) {
            return true;
        }

        $notifiable = $notification->notifiable;

        if ($notifiable === null) {
            return $this->markAsFailed(
                $notification,
                'Le destinataire de la notification est introuvable.'
            );
        }

        $phone = $notifiable->phone ?? null;

        if ($phone === null || trim($phone) === '') {
            return $this->markAsFailed(
                $notification,
                'Le numéro de téléphone du destinataire est requis.'
            );
        }

        try {
            /*
             * SMS provider integration will be added here.
             *
             * For now, we consider the SMS successfully dispatched
             * after validating the recipient.
             */

            $notification->markAsSent();

            return true;
        } catch (\Throwable $exception) {
            return $this->markAsFailed(
                $notification,
                $exception->getMessage()
            );
        }
    }

    /**
     * Mark the notification as failed.
     */
    protected function markAsFailed(
        Notification $notification,
        string $message
    ): bool {
        $notification->markAsFailed($message);

        return false;
    }
}