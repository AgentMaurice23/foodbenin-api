<?php

namespace App\Services\Notification\Templates;

use App\Enums\NotificationTypeEnum;
use InvalidArgumentException;

/**
 * Builds notification content from a notification type.
 *
 * This class is responsible only for generating the human-readable
 * title and message of a notification.
 *
 * It does not persist notifications and does not send them.
 */
class NotificationTemplate
{
    /**
     * Build a notification template.
     *
     * @return array{
     *     title: string,
     *     message: string,
     *     data: array
     * }
     */
    public function make(
        NotificationTypeEnum $type,
        array $data = []
    ): array {
        return match ($type) {
            /*
             * ---------------------------------------------------------
             * Order lifecycle
             * ---------------------------------------------------------
             */

            NotificationTypeEnum::ORDER_CREATED =>
                $this->orderCreated($data),

            NotificationTypeEnum::ORDER_CONFIRMED =>
                $this->orderConfirmed($data),

            NotificationTypeEnum::ORDER_PREPARING =>
                $this->orderPreparing($data),

            NotificationTypeEnum::ORDER_READY =>
                $this->orderReady($data),

            NotificationTypeEnum::ORDER_ASSIGNED =>
                $this->orderAssigned($data),

            NotificationTypeEnum::ORDER_PICKED_UP =>
                $this->orderPickedUp($data),

            NotificationTypeEnum::ORDER_DELIVERED =>
                $this->orderDelivered($data),

            NotificationTypeEnum::ORDER_CANCELLED =>
                $this->orderCancelled($data),

            /*
             * ---------------------------------------------------------
             * Delivery lifecycle
             * ---------------------------------------------------------
             */

            NotificationTypeEnum::DELIVERY_ASSIGNED =>
                $this->deliveryAssigned($data),

            NotificationTypeEnum::DELIVERY_STATUS_CHANGED =>
                $this->deliveryStatusChanged($data),

            NotificationTypeEnum::DELIVERY_STARTED =>
                $this->deliveryStarted($data),

            NotificationTypeEnum::DELIVERY_COMPLETED =>
                $this->deliveryCompleted($data),

            NotificationTypeEnum::DELIVERY_FAILED =>
                $this->deliveryFailed($data),

            NotificationTypeEnum::DELIVERY_CANCELLED =>
                $this->deliveryCancelled($data),

            /*
             * ---------------------------------------------------------
             * Payment lifecycle
             * ---------------------------------------------------------
             */

            NotificationTypeEnum::PAYMENT_PENDING =>
                $this->paymentPending($data),

            NotificationTypeEnum::PAYMENT_PROCESSING =>
                $this->paymentProcessing($data),

            NotificationTypeEnum::PAYMENT_PAID =>
                $this->paymentPaid($data),

            NotificationTypeEnum::PAYMENT_FAILED =>
                $this->paymentFailed($data),

            NotificationTypeEnum::PAYMENT_REFUNDED =>
                $this->paymentRefunded($data),
        };
    }

    /*
     * =============================================================
     * ORDER TEMPLATES
     * =============================================================
     */

    protected function orderCreated(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Commande reçue',
            "Votre commande {$orderNumber} a bien été reçue.",
            $data
        );
    }

    protected function orderConfirmed(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Commande confirmée',
            "Votre commande {$orderNumber} a été confirmée.",
            $data
        );
    }

    protected function orderPreparing(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Commande en préparation',
            "Votre commande {$orderNumber} est en cours de préparation.",
            $data
        );
    }

    protected function orderReady(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Commande prête',
            "Votre commande {$orderNumber} est prête.",
            $data
        );
    }

    protected function orderAssigned(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Commande affectée',
            "Votre commande {$orderNumber} a été affectée.",
            $data
        );
    }

    protected function orderPickedUp(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Commande récupérée',
            "Votre commande {$orderNumber} a été récupérée.",
            $data
        );
    }

    protected function orderDelivered(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Commande livrée',
            "Votre commande {$orderNumber} a été livrée.",
            $data
        );
    }

    protected function orderCancelled(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Commande annulée',
            "Votre commande {$orderNumber} a été annulée.",
            $data
        );
    }

    /*
     * =============================================================
     * DELIVERY TEMPLATES
     * =============================================================
     */

    protected function deliveryAssigned(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        $driverName = $this->required(
            $data,
            'driver_name'
        );

        return $this->build(
            'Livreur assigné',
            "Le livreur {$driverName} a été assigné à la commande {$orderNumber}.",
            $data
        );
    }

    protected function deliveryStatusChanged(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        $status = $this->required(
            $data,
            'status'
        );

        return $this->build(
            'Statut de livraison mis à jour',
            "Le statut de livraison de la commande {$orderNumber} est maintenant : {$status}.",
            $data
        );
    }

    protected function deliveryStarted(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Livraison en cours',
            "La livraison de votre commande {$orderNumber} a commencé.",
            $data
        );
    }

    protected function deliveryCompleted(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Livraison terminée',
            "La livraison de votre commande {$orderNumber} est terminée.",
            $data
        );
    }

    protected function deliveryFailed(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        $reason = $this->required(
            $data,
            'reason'
        );

        return $this->build(
            'Livraison échouée',
            "La livraison de la commande {$orderNumber} a échoué. Motif : {$reason}.",
            $data
        );
    }

    protected function deliveryCancelled(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Livraison annulée',
            "La livraison de la commande {$orderNumber} a été annulée.",
            $data
        );
    }

    /*
     * =============================================================
     * PAYMENT TEMPLATES
     * =============================================================
     */

    protected function paymentPending(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Paiement en attente',
            "Le paiement de la commande {$orderNumber} est en attente.",
            $data
        );
    }

    protected function paymentProcessing(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Paiement en cours',
            "Le paiement de la commande {$orderNumber} est en cours de traitement.",
            $data
        );
    }

    protected function paymentPaid(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Paiement confirmé',
            "Le paiement de la commande {$orderNumber} a été confirmé.",
            $data
        );
    }

    protected function paymentFailed(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        $reason = $this->required(
            $data,
            'reason'
        );

        return $this->build(
            'Paiement échoué',
            "Le paiement de la commande {$orderNumber} a échoué. Motif : {$reason}.",
            $data
        );
    }

    protected function paymentRefunded(array $data): array
    {
        $orderNumber = $this->required(
            $data,
            'order_number'
        );

        return $this->build(
            'Paiement remboursé',
            "Le paiement de la commande {$orderNumber} a été remboursé.",
            $data
        );
    }

    /*
     * =============================================================
     * HELPERS
     * =============================================================
     */

    /**
     * Build the final template structure.
     */
    protected function build(
        string $title,
        string $message,
        array $data
    ): array {
        return [
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * Retrieve a required template value.
     */
    protected function required(
        array $data,
        string $key
    ): string {
        if (
            !array_key_exists($key, $data)
            || $data[$key] === null
            || trim((string) $data[$key]) === ''
        ) {
            throw new InvalidArgumentException(
                "La donnée '{$key}' est obligatoire pour ce template de notification."
            );
        }

        return trim((string) $data[$key]);
    }
}