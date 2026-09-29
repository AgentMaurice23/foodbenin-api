<?php

namespace App\Services\Payment\Providers;

use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentProviderInterface;
use App\Services\Payment\DTO\PaymentResult;

/**
 * Payment provider for Moov Money.
 *
 * The real Moov API integration will be implemented later.
 */
class MoovPaymentProvider implements PaymentProviderInterface
{
    /**
     * Initialize a payment through Moov.
     */
    public function initializePayment(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: 'processing',
            message: 'Paiement Moov initialisé.',
            metadata: [
                'provider' => 'moov',
                'payment_id' => $payment->id,
            ],
        );
    }

    /**
     * Check the status of a Moov payment.
     */
    public function checkStatus(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: $payment->status->value,
            transactionReference:
                $payment->transaction_reference,
            message: 'Statut Moov récupéré.',
            metadata: [
                'provider' => 'moov',
                'payment_id' => $payment->id,
            ],
        );
    }

    /**
     * Verify a Moov payment.
     */
    public function verifyPayment(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: 'paid',
            transactionReference:
                $payment->transaction_reference,
            message: 'Paiement Moov vérifié.',
            metadata: [
                'provider' => 'moov',
                'payment_id' => $payment->id,
            ],
        );
    }

    /**
     * Refund a Moov payment.
     */
    public function refund(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: 'refunded',
            transactionReference:
                $payment->transaction_reference,
            message: 'Remboursement Moov effectué.',
            metadata: [
                'provider' => 'moov',
                'payment_id' => $payment->id,
            ],
        );
    }
}