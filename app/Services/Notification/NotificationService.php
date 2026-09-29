<?php

namespace App\Services\Notification;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use App\Services\Notification\NotificationDispatcher;
/**
 * Central service responsible for managing FoodBenin notifications.
 *
 * This service handles the notification persistence lifecycle.
 *
 * It intentionally does not communicate directly with external
 * providers such as SMS, WhatsApp, email or push providers yet.
 *
 * Those delivery mechanisms will be implemented through dedicated
 * notification channels later in the Notification Engine.
 */
class NotificationService
{
    /**
     * Create and persist a notification.
     *
     * The notification can be attached to any Eloquent model
     * through the polymorphic "notifiable" relationship.
     *
     * Examples:
     *
     * - User
     * - Order
     * - Payment
     * - Delivery
     */
    public function create(
        Model $notifiable,
        NotificationTypeEnum $type,
        NotificationChannelEnum $channel,
        string $title,
        string $message,
        array $data = [],
    ): Notification {
        $this->validateNotifiable($notifiable);

        return DB::transaction(function () use (
            $notifiable,
            $type,
            $channel,
            $title,
            $message,
            $data,
        ) {
            return Notification::query()->create([
                'type' => $type,
                'channel' => $channel,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'notifiable_type' => $notifiable::class,
                'notifiable_id' => $notifiable->getKey(),
                'read_at' => null,
                'sent_at' => null,
                'failed_at' => null,
                'error_message' => null,
            ]);
        });
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(
        Notification $notification
    ): Notification {
        $notification->markAsRead();

        return $notification->fresh();
    }

    /**
     * Mark a notification as successfully sent.
     */
    public function markAsSent(
        Notification $notification
    ): Notification {
        $notification->markAsSent();

        return $notification->fresh();
    }

    /**
     * Mark a notification as failed.
     */
    public function markAsFailed(
        Notification $notification,
        string $error
    ): Notification {
        if (trim($error) === '') {
            throw new InvalidArgumentException(
                'Le message d\'erreur de notification est obligatoire.'
            );
        }

        $notification->markAsFailed($error);

        return $notification->fresh();
    }

    /**
     * Validate the notifiable model.
     */
    protected function validateNotifiable(
        Model $notifiable
    ): void {
        if (!$notifiable->exists) {
            throw new InvalidArgumentException(
                'Le modèle destinataire doit être enregistré en base de données.'
            );
        }

        if (!$notifiable->getKey()) {
            throw new InvalidArgumentException(
                'Le modèle destinataire doit posséder une clé primaire.'
            );
        }
    }

    /**
     * Create and dispatch a notification.
     *
     * This is the main entry point for the FoodBenin
     * Notification Engine.
     *
     * It creates the notification first, then delegates
     * the delivery to the NotificationDispatcher.
     *
     * @param Model $notifiable
     * @param NotificationTypeEnum $type
     * @param NotificationChannelEnum $channel
     * @param string $title
     * @param string $message
     * @param array $data
     *
     * @return Notification
     */
    public function notify(
        Model $notifiable,
        NotificationTypeEnum $type,
        NotificationChannelEnum $channel,
        string $title,
        string $message,
        array $data = [],
    ): Notification {
        $notification = $this->create(
            $notifiable,
            $type,
            $channel,
            $title,
            $message,
            $data,
        );

        try {
            app(NotificationDispatcher::class)
                ->dispatch($notification);
        } catch (\Throwable $exception) {
            $this->markAsFailed(
                $notification,
                $exception->getMessage()
            );
        }

        return $notification->fresh();
    }
}
