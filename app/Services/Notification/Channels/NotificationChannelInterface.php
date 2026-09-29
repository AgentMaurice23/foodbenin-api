<?php

namespace App\Services\Notification\Channels;

use App\Models\Notification;

/**
 * Contract for all FoodBenin notification channels.
 *
 * Every notification channel must implement the send()
 * method so that the Notification Engine can communicate
 * with different delivery mechanisms through a common API.
 */
interface NotificationChannelInterface
{
    /**
     * Send a notification through the current channel.
     *
     * The actual implementation depends on the channel:
     *
     * - Database
     * - Email
     * - SMS
     * - WhatsApp
     * - Push
     *
     * @return bool
     */
    public function send(Notification $notification): bool;
}