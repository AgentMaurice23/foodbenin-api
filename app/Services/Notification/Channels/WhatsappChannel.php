<?php

namespace App\Services\Notification\Channels;

use App\Models\Notification;

class WhatsappChannel implements NotificationChannelInterface
{
    /**
     * Send a notification through WhatsApp.
     *
     * The real WhatsApp provider will be integrated later.
     * For now, the channel validates the recipient and
     * records the notification as successfully sent.
     */
    public function send(Notification $notification): bool
    {
        /*
         * Idempotency:
         * An already sent notification must not be sent again.
         */
        if ($notification->sent_at !== null) {
            return true;
        }

        $notifiable = $notification->notifiable;

        /*
         * The notification must have a valid recipient.
         */
        if ($notifiable === null) {
            return $this->markAsFailed(
                $notification,
                'Le destinataire de la notification est introuvable.'
            );
        }

        $phone = $notifiable->phone ?? null;

        /*
         * WhatsApp requires a phone number.
         */
        if ($phone === null || trim($phone) === '') {
            return $this->markAsFailed(
                $notification,
                'Le numéro de téléphone du destinataire est requis pour WhatsApp.'
            );
        }

        try {
            /*
             * Real WhatsApp provider integration will be added here.
             *
             * Example later:
             *
             * $this->provider->send(
             *     phone: $phone,
             *     message: $notification->message,
             * );
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