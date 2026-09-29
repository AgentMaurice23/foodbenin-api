<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Services\Payment\Webhook\PaymentWebhookPayload;
use App\Services\Payment\Webhook\PaymentWebhookSecurityService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentWebhookService
{
    /**
     * Create a new payment webhook service.
     */
    public function __construct(
        protected PaymentService $paymentService,
        protected PaymentWebhookSecurityService $securityService,
    ) {
    }

        /**
     * Process a payment webhook event.
     *
     * Validates the webhook, guarantees event idempotence,
     * persists the event and processes the associated payment.
     *
     * Already processed events are returned immediately.
     * Existing unprocessed events are retried.
     */
    public function process(
        string $provider,
        string $eventId,
        string $eventType,
        Payment $payment,
        array $payload,
        ?string $transactionReference = null,
        ?float $amount = null,
        ?int $paymentId = null,
    ): PaymentWebhookEvent {
        $webhook = new PaymentWebhookPayload(
            provider: $provider,
            eventId: $eventId,
            eventType: $eventType,
            payment: $payment,
            payload: $payload,
            transactionReference: $transactionReference,
            amount: $amount,
        );

        /*
        * Validate the webhook before creating or processing
        * any database event.
        */
        $this->securityService->validate($webhook);

        /*
        * Check whether this exact provider event already exists.
        */
        $existingEvent = PaymentWebhookEvent::query()
            ->where('provider', $provider)
            ->where('event_id', $eventId)
            ->first();

        /*
        * A processed event is already successfully handled.
        *
        * Return it immediately to guarantee idempotence.
        */
        if (
            $existingEvent
            && $existingEvent->processed_at !== null
        ) {
            return $existingEvent->fresh([
                'payment',
            ]);
        }

        /*
        * If the event exists but has not been processed,
        * we reuse it and retry the payment processing.
        */
        if ($existingEvent) {
            return DB::transaction(function () use (
                $existingEvent,
                $webhook
            ) {
                $event = PaymentWebhookEvent::query()
                    ->lockForUpdate()
                    ->findOrFail($existingEvent->id);

                /*
                * Another concurrent process may have completed
                * this event while we were waiting for the lock.
                */
                if ($event->processed_at !== null) {
                    return $event->fresh([
                        'payment',
                    ]);
                }

                $this->handlePayment(
                    $event,
                    $webhook->payment,
                    $webhook->transactionReference
                );

                $event->processed_at = now();
                $event->save();

                return $event->fresh([
                    'payment',
                ]);
            });
        }

        /*
        * No existing event:
        * create and process the webhook atomically.
        */
        try {
            return DB::transaction(function () use ($webhook) {
                $event = PaymentWebhookEvent::query()->create([
                    'payment_id' => $webhook->payment->id,
                    'provider' => $webhook->provider,
                    'event_id' => $webhook->eventId,
                    'event_type' => $webhook->eventType,
                    'transaction_reference' =>
                        $webhook->transactionReference,
                    'payload' => $webhook->payload,
                ]);

                $this->handlePayment(
                    $event,
                    $webhook->payment,
                    $webhook->transactionReference
                );

                $event->processed_at = now();
                $event->save();

                return $event->fresh([
                    'payment',
                ]);
            });
        } catch (QueryException $exception) {
            /*
            * Two requests may receive the same webhook
            * simultaneously.
            *
            * If another request created the event first,
            * return that event instead of failing with
            * a duplicate-key exception.
            */
            $existingEvent = PaymentWebhookEvent::query()
                ->where('provider', $provider)
                ->where('event_id', $eventId)
                ->first();

            if ($existingEvent) {
                return $existingEvent->fresh([
                    'payment',
                ]);
            }

            throw $exception;
        }
    }

    /**
     * Handle the payment operation associated with a webhook.
     *
     * @param PaymentWebhookEvent $event
     * @param Payment $payment
     * @param string|null $transactionReference
     *
     * @return void
     */
    protected function handlePayment(
        PaymentWebhookEvent $event,
        Payment $payment,
        ?string $transactionReference
    ): void {
        match ($event->event_type) {
            'payment.success' => $this->paymentService->markAsPaid(
                $payment,
                $transactionReference
            ),

            'payment.failed' => $this->paymentService->markAsFailed(
                $payment
            ),

            'payment.refunded' => $this->paymentService->refund(
                $payment
            ),

            default => throw new RuntimeException(
                "Type d'événement de paiement inconnu : {$event->event_type}."
            ),
        };
    }
}