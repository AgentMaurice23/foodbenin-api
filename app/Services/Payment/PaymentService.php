<?php

namespace App\Services\Payment;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\DTO\PaymentResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentService
{
    /**
     * Payment provider resolver.
     */
    protected PaymentProviderResolver $providerResolver;

    /**
     * Payment workflow service.
     */
    protected PaymentWorkflowService $workflow;

    /**
     * Create a new PaymentService instance.
     */
    public function __construct(
        PaymentProviderResolver $providerResolver,
        PaymentWorkflowService $workflow
    ) {
        $this->providerResolver = $providerResolver;
        $this->workflow = $workflow;
    }

    /**
     * Create a new pending payment for an order.
     *
     * @throws RuntimeException
     */
    public function create(
        Order $order,
        PaymentMethodEnum $paymentMethod
    ): Payment {
        return DB::transaction(function () use (
            $order,
            $paymentMethod
        ) {
            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            $this->validateOrder($order);

            $existingPayment = Payment::query()
                ->where('order_id', $order->id)
                ->whereIn('status', [
                    PaymentStatusEnum::PENDING,
                    PaymentStatusEnum::PROCESSING,
                    PaymentStatusEnum::PAID,
                ])
                ->lockForUpdate()
                ->first();

            if ($existingPayment) {
                throw new RuntimeException(
                    'Cette commande possède déjà un paiement actif.'
                );
            }

            return Payment::query()->create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'restaurant_id' => $order->restaurant_id,
                'amount' => $order->total,
                'payment_method' => $paymentMethod,
                'uuid' => (string) Str::uuid(),
                'status' => PaymentStatusEnum::PENDING,
            ]);
        });
    }

    /**
     * Initialize a pending payment through its provider.
     *
     * The workflow service is responsible for the
     * PENDING → PROCESSING transition.
     *
     * @throws RuntimeException
     */
    public function initialize(
        Payment $payment
    ): Payment {
        return DB::transaction(function () use ($payment) {
            $payment = $this->lockPayment($payment);

            $provider = $this->providerResolver->resolve(
                $payment->payment_method
            );

            $result = $provider->initializePayment($payment);

            if (!$result->success) {
                throw new RuntimeException(
                    $result->message
                    ?? 'Impossible d\'initialiser le paiement.'
                );
            }

            $payment = $this->workflow->initialize($payment);

            if ($result->transactionReference) {
                $payment->transaction_reference =
                    $result->transactionReference;

                $payment->save();
            }

            return $payment->fresh([
                'order',
                'user',
                'restaurant',
            ]);
        });
    }

    /**
     * Marks a payment as paid in an idempotent manner.
     *
     * If the payment is already paid:
     * - the same transaction reference is accepted as an idempotent retry;
     * - a different transaction reference is rejected.
     *
     * @param Payment $payment
     * @param string|null $transactionReference
     * @return Payment
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

            /*
            * Idempotence:
            *
            * The same successful webhook may be received multiple times.
            * If the payment is already PAID with the same transaction
            * reference, there is nothing left to do.
            */
            if ($payment->status === PaymentStatusEnum::PAID) {

                if (
                    $transactionReference !== null
                    && $payment->transaction_reference === $transactionReference
                ) {
                    return $payment->fresh([
                        'order',
                        'user',
                        'restaurant',
                    ]);
                }

                /*
                * A different transaction reference for an already paid
                * payment indicates an inconsistency and must be rejected.
                */
                throw new RuntimeException(
                    'Ce paiement est déjà confirmé avec une autre référence de transaction.'
                );
            }

            /*
            * Normal workflow:
            *
            * PENDING / PROCESSING → PAID
            */
            $payment = $this->workflow->markAsPaid(
                $payment,
                $transactionReference
            );

            /*
            * Keep the order payment state synchronized.
            */
            $order = $payment->order;

            if ($order) {
                $order->is_paid = true;
                $order->save();
            }

            return $payment->fresh([
                'order',
                'user',
                'restaurant',
            ]);
        });
    }

    /**
     * Mark a payment as failed.
     *
     * The workflow service is responsible for validating
     * the PENDING/PROCESSING → FAILED transition.
     *
     * @throws RuntimeException
     */
    public function markAsFailed(
        Payment $payment
    ): Payment {
        return DB::transaction(function () use ($payment) {
            $payment = $this->lockPayment($payment);

            $payment = $this->workflow->fail($payment);

            return $payment->fresh([
                'order',
                'user',
                'restaurant',
            ]);
        });
    }

    /**
     * Refund a paid payment through its provider.
     *
     * The provider performs the external refund operation.
     * The workflow service performs the PAID → REFUNDED transition.
     *
     * @throws RuntimeException
     */
    public function refund(
        Payment $payment
    ): Payment {
        return DB::transaction(function () use ($payment) {
            $payment = $this->lockPayment($payment);

            $provider = $this->providerResolver->resolve(
                $payment->payment_method
            );

            $result = $provider->refund($payment);

            if (!$result->success) {
                throw new RuntimeException(
                    $result->message
                    ?? 'Impossible de rembourser le paiement.'
                );
            }

            $payment = $this->workflow->refund($payment);

            $order = $payment->order;

            if ($order) {
                $order->is_paid = false;
                $order->save();
            }

            return $payment->fresh([
                'order',
                'user',
                'restaurant',
            ]);
        });
    }

    /**
     * Check the current status of a payment through its provider.
     *
     * This operation does not modify the payment.
     */
    public function checkStatus(
        Payment $payment
    ): PaymentResult {
        $payment = $payment->fresh();

        $provider = $this->providerResolver->resolve(
            $payment->payment_method
        );

        return $provider->checkStatus($payment);
    }

    /**
     * Verify a payment through its provider.
     *
     * This operation retrieves the provider confirmation.
     */
    public function verify(
        Payment $payment
    ): PaymentResult {
        $payment = $payment->fresh();

        $provider = $this->providerResolver->resolve(
            $payment->payment_method
        );

        return $provider->verifyPayment($payment);
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

    /**
     * Validate that an order can receive a payment.
     *
     * @throws RuntimeException
     */
    protected function validateOrder(
        Order $order
    ): void {
        if ($order->total <= 0) {
            throw new RuntimeException(
                'Le montant de la commande doit être supérieur à zéro.'
            );
        }

        if ($order->is_paid) {
            throw new RuntimeException(
                'Cette commande est déjà payée.'
            );
        }

        if (!$order->user_id) {
            throw new RuntimeException(
                'Cette commande ne possède aucun client.'
            );
        }

        if (!$order->restaurant_id) {
            throw new RuntimeException(
                'Cette commande ne possède aucun restaurant.'
            );
        }
    }
}