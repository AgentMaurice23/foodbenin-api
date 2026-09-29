<?php

namespace App\Services\Payment\Providers;

use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentProviderInterface;
use App\Services\Payment\DTO\PaymentResult;

/**
 * Payment provider for MTN Mobile Money.
 *
 * The real MTN API integration will be implemented later.
 */
class MtnPaymentProvider implements PaymentProviderInterface
{
    /**
     * Initialize a payment through MTN.
     */
    public function initializePayment(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: 'processing',
            message: 'Paiement MTN initialisé.',
            metadata: [
                'provider' => 'mtn',
                'payment_id' => $payment->id,
            ],
        );
    }

    /**
     * Check the status of an MTN payment.
     */
    public function checkStatus(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: $payment->status->value,
            transactionReference:
                $payment->transaction_reference,
            message: 'Statut MTN récupéré.',
            metadata: [
                'provider' => 'mtn',
                'payment_id' => $payment->id,
            ],
        );
    }

    /**
     * Verify an MTN payment.
     */
    public function verifyPayment(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: 'paid',
            transactionReference:
                $payment->transaction_reference,
            message: 'Paiement MTN vérifié.',
            metadata: [
                'provider' => 'mtn',
                'payment_id' => $payment->id,
            ],
        );
    }

    /**
     * Refund an MTN payment.
     */
    public function refund(
        Payment $payment
    ): PaymentResult {
        return PaymentResult::success(
            status: 'refunded',
            transactionReference:
                $payment->transaction_reference,
            message: 'Remboursement MTN effectué.',
            metadata: [
                'provider' => 'mtn',
                'payment_id' => $payment->id,
            ],
        );
    }
}