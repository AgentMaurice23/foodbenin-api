<?php

namespace App\Services\Delivery;

use App\Models\Delivery;
use App\Notifications\DeliveryNotification;
use Illuminate\Support\Facades\Notification;

class DeliveryNotificationService
{
    /**
     * Notifie le livreur qu'une livraison lui a été assignée.
     */
    public function driverAssigned(
        Delivery $delivery
    ): void {

        $driver = $delivery->driver;

        if (!$driver || !$driver->user) {
            return;
        }

        $driver->user->notify(
            new DeliveryNotification(
                $delivery,
                'delivery_assigned',
                'Une nouvelle livraison vous a été assignée.'
            )
        );
    }

    /**
     * Notifie le livreur que sa livraison a été acceptée.
     */
    public function deliveryAccepted(
        Delivery $delivery
    ): void {

        $driver = $delivery->driver;

        if (!$driver || !$driver->user) {
            return;
        }

        $driver->user->notify(
            new DeliveryNotification(
                $delivery,
                'delivery_accepted',
                'La livraison a été acceptée.'
            )
        );
    }

    /**
     * Notifie le client du changement de statut.
     */
    public function customerStatusChanged(
        Delivery $delivery
    ): void {

        $customer = $delivery->order?->user;

        if (!$customer) {
            return;
        }

        $customer->notify(
            new DeliveryNotification(
                $delivery,
                'delivery_status_changed',
                'Le statut de votre livraison a été mis à jour.'
            )
        );
    }

    /**
     * Notifie le client que la commande est en cours de livraison.
     */
    public function deliveryStarted(
        Delivery $delivery
    ): void {

        $customer = $delivery->order?->user;

        if (!$customer) {
            return;
        }

        $customer->notify(
            new DeliveryNotification(
                $delivery,
                'delivery_started',
                'Votre commande est maintenant en cours de livraison.'
            )
        );
    }

    /**
     * Notifie le client lorsque la livraison est terminée.
     */
    public function deliveryCompleted(
        Delivery $delivery
    ): void {

        $customer = $delivery->order?->user;

        if (!$customer) {
            return;
        }

        $customer->notify(
            new DeliveryNotification(
                $delivery,
                'delivery_completed',
                'Votre commande a été livrée.'
            )
        );
    }

    /**
     * Notifie le client lorsqu'une livraison échoue.
     */
    public function deliveryFailed(
        Delivery $delivery
    ): void {

        $customer = $delivery->order?->user;

        if (!$customer) {
            return;
        }

        $customer->notify(
            new DeliveryNotification(
                $delivery,
                'delivery_failed',
                'La livraison de votre commande a échoué.'
            )
        );
    }

    /**
     * Notifie le client lorsqu'une livraison est annulée.
     */
    public function deliveryCancelled(
        Delivery $delivery
    ): void {

        $customer = $delivery->order?->user;

        if (!$customer) {
            return;
        }

        $customer->notify(
            new DeliveryNotification(
                $delivery,
                'delivery_cancelled',
                'La livraison de votre commande a été annulée.'
            )
        );
    }
}