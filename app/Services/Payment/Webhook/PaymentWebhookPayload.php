<?php

namespace App\Services\Payment\Webhook;

use App\Models\Payment;
use RuntimeException;

class PaymentWebhookPayload
{
    /**
     * Create a normalized webhook payload.
     *
     * @param string $provider
     * @param string $eventId
     * @param string $eventType
     * @param Payment $payment
     * @param array<string, mixed> $payload
     * @param string|null $transactionReference
     * @param float|null $amount
     */
    public function __construct(
        public readonly string $provider,
        public readonly string $eventId,
        public readonly string $eventType,
        public readonly Payment $payment,
        public readonly array $payload,
        public readonly ?string $transactionReference = null,
        public readonly ?float $amount = null,
    ) {
        $this->validate();
    }

    /**
     * Validate the normalized webhook payload.
     *
     * @throws RuntimeException
     */
    protected function validate(): void
    {
        if (trim($this->provider) === '') {
            throw new RuntimeException(
                'Le provider du webhook est obligatoire.'
            );
        }

        if (trim($this->eventId) === '') {
            throw new RuntimeException(
                'L\'identifiant du webhook est obligatoire.'
            );
        }

        if (trim($this->eventType) === '') {
            throw new RuntimeException(
                'Le type d\'événement du webhook est obligatoire.'
            );
        }

        if (empty($this->payload)) {
            throw new RuntimeException(
                'Le payload du webhook ne peut pas être vide.'
            );
        }

        if (
            $this->amount !== null
            && $this->amount < 0
        ) {
            throw new RuntimeException(
                'Le montant du webhook est invalide.'
            );
        }
    }
}