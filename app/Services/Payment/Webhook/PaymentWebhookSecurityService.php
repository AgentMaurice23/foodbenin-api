<?php

namespace App\Services\Payment\Webhook;

use App\Models\Payment;
use RuntimeException;

class PaymentWebhookSecurityService
{
    /**
     * Providers currently authorized to send payment webhooks.
     *
     * @var array<int, string>
     */
    protected array $allowedProviders = [
        'mtn',
        'moov',
        'celtis',
    ];

    /**
     * Validate the provider.
     *
     * @param string $provider
     *
     * @throws RuntimeException
     */
    public function validateProvider(string $provider): void
    {
        if (
            !in_array(
                strtolower($provider),
                $this->allowedProviders,
                true
            )
        ) {
            throw new RuntimeException(
                "Provider de paiement non autorisé : {$provider}."
            );
        }
    }

    /**
     * Validate the webhook event identifier.
     *
     * @param string $eventId
     *
     * @throws RuntimeException
     */
    public function validateEventId(string $eventId): void
    {
        if (trim($eventId) === '') {
            throw new RuntimeException(
                'L\'identifiant de l\'événement est obligatoire.'
            );
        }
    }

    /**
     * Validate the webhook event type.
     *
     * @param string $eventType
     *
     * @throws RuntimeException
     */
    public function validateEventType(string $eventType): void
    {
        $allowedTypes = [
            'payment.success',
            'payment.failed',
            'payment.refunded',
        ];

        if (
            !in_array(
                $eventType,
                $allowedTypes,
                true
            )
        ) {
            throw new RuntimeException(
                "Type d'événement non autorisé : {$eventType}."
            );
        }
    }

    /**
     * Validate that the payment belongs to the webhook context.
     *
     * @param Payment $payment
     * @param int $paymentId
     *
     * @throws RuntimeException
     */
    public function validatePayment(
        Payment $payment,
        int $paymentId
    ): void {
        if ($payment->id !== $paymentId) {
            throw new RuntimeException(
                'Le paiement ne correspond pas à l\'événement webhook.'
            );
        }
    }

    /**
     * Validate the webhook amount against the payment amount.
     *
     * @param Payment $payment
     * @param float|null $amount
     *
     * @throws RuntimeException
     */
    public function validateAmount(
        Payment $payment,
        ?float $amount
    ): void {
        /*
         * Some webhook events may not contain an amount.
         * In that case, validation is skipped here.
         */
        if ($amount === null) {
            return;
        }

        /*
         * Convert both values to integer cents to avoid
         * floating-point comparison problems.
         */
        $paymentAmount = (int) round(
            (float) $payment->amount * 100
        );

        $webhookAmount = (int) round(
            $amount * 100
        );

        if ($paymentAmount !== $webhookAmount) {
            throw new RuntimeException(
                'Le montant du webhook ne correspond pas au montant du paiement.'
            );
        }
    }

        /**
     * Vérifie la cohérence de la référence de transaction.
     *
     * Si le paiement ne possède encore aucune référence,
     * le webhook peut fournir la première référence.
     *
     * Si une référence existe déjà, celle du webhook doit
     * obligatoirement correspondre.
     */
    public function validateTransactionReference(
        Payment $payment,
        ?string $transactionReference
    ): void {
        /*
        * Aucune référence fournie par le webhook :
        * il n'y a rien à comparer.
        */
        if ($transactionReference === null) {
            return;
        }

        /*
        * Le paiement ne possède encore aucune référence.
        * Le webhook peut donc fournir la première référence.
        */
        if ($payment->transaction_reference === null) {
            return;
        }

        /*
        * Une référence existe déjà sur le paiement.
        * Elle doit correspondre exactement à celle du webhook.
        */
        if (
            $payment->transaction_reference !== $transactionReference
        ) {
            throw new RuntimeException(
                'La référence de transaction du webhook ne correspond pas au paiement.'
            );
        }
    }

    /**
     * Validate the complete normalized webhook payload.
     *
     * @param PaymentWebhookPayload $webhook
     *
     * @throws RuntimeException
     */
    public function validate(
        PaymentWebhookPayload $webhook
    ): void {
        $this->validateProvider(
            $webhook->provider
        );

        $this->validateEventId(
            $webhook->eventId
        );

        $this->validateEventType(
            $webhook->eventType
        );

        $this->validateAmount(
            $webhook->payment,
            $webhook->amount
        );

        $this->validateTransactionReference(
            $webhook->payment,
            $webhook->transactionReference
        );
    }
}