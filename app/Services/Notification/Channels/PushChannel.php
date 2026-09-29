<?php

namespace App\Services\Notification\Channels;

use App\Models\Notification;
use RuntimeException;

class PushChannel implements NotificationChannelInterface
{
    /**
     * Send a notification through the push channel.
     */
    public function send(Notification $notification): bool
    {
        /*
         * Une notification Push doit obligatoirement
         * avoir un modèle notifiable.
         */
        if (!$notification->notifiable) {
            throw new RuntimeException(
                'Impossible d\'envoyer la notification push : notifiable introuvable.'
            );
        }

        /*
         * L'intégration réelle avec le fournisseur Push
         * (FCM, OneSignal, etc.) sera branchée ici.
         *
         * Pour le moment, nous considérons l'envoi
         * comme réussi lorsque le notifiable existe.
         */

        $notification->markAsSent();

        return true;
    }
}