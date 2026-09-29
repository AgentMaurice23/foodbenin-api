<?php

namespace App\Services\Payment\Providers;

use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentProviderInterface;
use App\Services\Payment\DTO\PaymentResult;

/**
 * Payment provider for Celtis.
 *
 * The real Celtis API integration will be implemented later.
 */
class CeltisPaymentProvider implements PaymentProviderInterface
{
    /**
     * Initialize a payment through Celtis.
     */
    public function initializePayment(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: 'processing',
            message: 'Paiement Celtis initialisé.',
            metadata: [
                'provider' => 'celtis',
                'payment_id' => $payment->id,
            ],
        );
    }

    /**
     * Check the status of a Celtis payment.
     */
    public function checkStatus(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: $payment->status->value,
            transactionReference:
                $payment->transaction_reference,
            message: 'Statut Celtis récupéré.',
            metadata: [
                'provider' => 'celtis',
                'payment_id' => $payment->id,
            ],
        );
    }

    /**
     * Verify a Celtis payment.
     */
    public function verifyPayment(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: 'paid',
            transactionReference:
                $payment->transaction_reference,
            message: 'Paiement Celtis vérifié.',
            metadata: [
                'provider' => 'celtis',
                'payment_id' => $payment->id,
            ],
        );
    }

    /**
     * Refund a Celtis payment.
     */
    public function refund(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: 'refunded',
            transactionReference:
                $payment->transaction_reference,
            message: 'Remboursement Celtis effectué.',
            metadata: [
                'provider' => 'celtis',
                'payment_id' => $payment->id,
            ],
        );
    }
}