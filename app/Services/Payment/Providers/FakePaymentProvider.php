<?php

namespace App\Services\Payment\Providers;

use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentProviderInterface;
use App\Services\Payment\DTO\PaymentResult;
use Illuminate\Support\Str;

/**
 * Fake payment provider used for automated tests
 * and local development.
 *
 * This provider does not communicate with any external API.
 */
class FakePaymentProvider implements PaymentProviderInterface
{
    /**
     * Initialize a fake payment.
     */
    public function initializePayment(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: 'processing',
            transactionReference: 'FAKE-' . Str::upper(
                Str::random(12)
            ),
            message: 'Paiement initialisé avec succès.',
            metadata: [
                'provider' => 'fake',
                'payment_id' => $payment->id,
            ],
        );
    }

    /**
     * Check the status of a fake payment.
     */
    public function checkStatus(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: $payment->status->value,
            transactionReference:
                $payment->transaction_reference,
            message: 'Statut du paiement récupéré.',
            metadata: [
                'provider' => 'fake',
                'payment_id' => $payment->id,
            ],
        );
    }

    /**
     * Verify a fake payment.
     */
    public function verifyPayment(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: 'paid',
            transactionReference:
                $payment->transaction_reference
                ?? 'FAKE-' . Str::upper(Str::random(12)),
            message: 'Paiement vérifié avec succès.',
            metadata: [
                'provider' => 'fake',
                'payment_id' => $payment->id,
            ],
        );
    }

    /**
     * Refund a fake payment.
     */
    public function refund(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: 'refunded',
            transactionReference:
                $payment->transaction_reference,
            message: 'Remboursement effectué avec succès.',
            metadata: [
                'provider' => 'fake',
                'payment_id' => $payment->id,
            ],
        );
    }
}