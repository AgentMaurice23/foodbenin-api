<?php

namespace App\Services\Payment\Contracts;

use App\Models\Payment;
use App\Services\Payment\DTO\PaymentResult;

/**
 * Contract that every payment provider must implement.
 *
 * Examples:
 * - MTN Mobile Money
 * - Moov Money
 * - Celtis
 * - Fake provider for automated tests
 */
interface PaymentProviderInterface
{
    /**
     * Initialize a payment with the external provider.
     */
    public function initializePayment(
        Payment $payment
    ): PaymentResult;

    /**
     * Check the current status of a payment.
     */
    public function checkStatus(
        Payment $payment
    ): PaymentResult;

    /**
     * Verify a payment after provider confirmation.
     */
    public function verifyPayment(
        Payment $payment
    ): PaymentResult;

    /**
     * Request a refund from the payment provider.
     */
    public function refund(
        Payment $payment
    ): PaymentResult;
}