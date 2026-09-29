<?php

namespace App\Services\Notification;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Notification;
use App\Services\Notification\Templates\NotificationTemplate;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class NotificationManager
{
    public function __construct(
        protected NotificationService $notificationService,
        protected NotificationTemplate $template,
        protected NotificationDispatcher $dispatcher,
    ) {
    }

    /**
     * Create and dispatch a notification.
     *
     * @return Notification
     */
    public function send(
        Model $notifiable,
        NotificationTypeEnum $type,
        NotificationChannelEnum $channel,
        array $data = [],
    ): Notification {
        if (!$notifiable->exists) {
            throw new InvalidArgumentException(
                'Le modèle destinataire doit être enregistré en base de données.'
            );
        }

        /*
         * 1. Build the notification content.
         */
        $template = $this->template->make(
            $type,
            $data
        );

        /*
         * 2. Persist the notification.
         */
        $notification = $this->notificationService->create(
            $notifiable,
            $type,
            $channel,
            $template['title'],
            $template['message'],
            $template['data'],
        );

        /*
         * 3. Dispatch through the configured channel.
         */
        $this->dispatcher->dispatch(
            $notification
        );

        /*
         * 4. Return the fresh notification state.
         */
        return $notification->fresh();
    }
}