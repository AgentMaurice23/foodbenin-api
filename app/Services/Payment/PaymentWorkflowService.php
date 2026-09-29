<?php

namespace App\Services\Payment;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Handles payment state transitions.
 *
 * This service centralizes the business rules governing
 * the payment lifecycle.
 *
 * Allowed lifecycle:
 *
 * PENDING
 *    ↓
 * PROCESSING
 *    ↓
 * PAID
 *    ↓
 * REFUNDED
 *
 * PENDING / PROCESSING
 *    ↓
 * FAILED
 */
class PaymentWorkflowService
{
    /**
     * Initialize a payment.
     *
     * Transition:
     *
     * PENDING → PROCESSING
     *
     * @throws RuntimeException
     */
    public function initialize(
        Payment $payment
    ): Payment {
        return DB::transaction(function () use ($payment) {
            $payment = $this->lockPayment($payment);

            $this->assertTransition(
                $payment,
                PaymentStatusEnum::PROCESSING
            );

            $payment->status = PaymentStatusEnum::PROCESSING;

            $payment->save();

            return $payment->fresh();
        });
    }

    /**
     * Mark a payment as paid.
     *
     * Allowed transitions:
     *
     * PENDING → PAID
     * PROCESSING → PAID
     *
     * @throws RuntimeException
     */
    public function markAsPaid(
        Payment $payment,
        ?string $transactionReference = null
    ): Payment {
        return DB::transaction(function () use (
            $payment,
            $transactionReference
        ) {
            $payment = $this->lockPayment($payment);

            $this->assertTransition(
                $payment,
                PaymentStatusEnum::PAID
            );

            $payment->status = PaymentStatusEnum::PAID;

            if ($transactionReference !== null) {
                $payment->transaction_reference =
                    $transactionReference;
            }

            $payment->paid_at = now();

            $payment->save();

            return $payment->fresh();
        });
    }

    /**
     * Mark a payment as failed.
     *
     * Allowed transitions:
     *
     * PENDING → FAILED
     * PROCESSING → FAILED
     *
     * @throws RuntimeException
     */
    public function fail(
        Payment $payment
    ): Payment {
        return DB::transaction(function () use ($payment) {
            $payment = $this->lockPayment($payment);

            $this->assertTransition(
                $payment,
                PaymentStatusEnum::FAILED
            );

            $payment->status = PaymentStatusEnum::FAILED;

            $payment->save();

            return $payment->fresh();
        });
    }

    /**
     * Refund a paid payment.
     *
     * Transition:
     *
     * PAID → REFUNDED
     *
     * @throws RuntimeException
     */
    public function refund(
        Payment $payment
    ): Payment {
        return DB::transaction(function () use ($payment) {
            $payment = $this->lockPayment($payment);

            $this->assertTransition(
                $payment,
                PaymentStatusEnum::REFUNDED
            );

            $payment->status = PaymentStatusEnum::REFUNDED;

            $payment->save();

            return $payment->fresh();
        });
    }

    /**
     * Determine whether a payment can transition
     * to the given target status.
     */
    public function canTransitionTo(
        Payment $payment,
        PaymentStatusEnum $targetStatus
    ): bool {
        return match ($payment->status) {
            PaymentStatusEnum::PENDING => in_array(
                $targetStatus,
                [
                    PaymentStatusEnum::PROCESSING,
                    PaymentStatusEnum::PAID,
                    PaymentStatusEnum::FAILED,
                ],
                true
            ),

            PaymentStatusEnum::PROCESSING => in_array(
                $targetStatus,
                [
                    PaymentStatusEnum::PAID,
                    PaymentStatusEnum::FAILED,
                ],
                true
            ),

            PaymentStatusEnum::PAID =>
                $targetStatus === PaymentStatusEnum::REFUNDED,

            PaymentStatusEnum::FAILED,
            PaymentStatusEnum::REFUNDED => false,
        };
    }

    /**
     * Validate a payment transition.
     *
     * @throws RuntimeException
     */
    protected function assertTransition(
        Payment $payment,
        PaymentStatusEnum $targetStatus
    ): void {
        if (
            !$this->canTransitionTo(
                $payment,
                $targetStatus
            )
        ) {
            throw new RuntimeException(
                sprintf(
                    'Transition de paiement invalide : %s → %s.',
                    $payment->status->value,
                    $targetStatus->value
                )
            );
        }
    }

    /**
     * Lock a payment row for update.
     */
    protected function lockPayment(
        Payment $payment
    ): Payment {
        return Payment::query()
            ->lockForUpdate()
            ->findOrFail($payment->id);
    }
}