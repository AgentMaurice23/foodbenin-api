<?php

namespace Tests\Feature;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Services\Payment\PaymentWebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentWebhookService $webhookService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->webhookService = app(
            PaymentWebhookService::class
        );
    }

    /**
     * A successful webhook marks the payment as paid.
     */
    public function test_successful_webhook_marks_payment_as_paid(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PROCESSING,
        ]);

        $event = $this->webhookService->process(
            provider: 'mtn',
            eventId: 'EVT-123456',
            eventType: 'payment.success',
            payment: $payment,
            payload: [
                'status' => 'success',
            ],
            transactionReference: 'TXN-123456',
        );

        $this->assertEquals(
            PaymentStatusEnum::PAID,
            $payment->fresh()->status
        );

        $this->assertNotNull(
            $event->processed_at
        );

        $this->assertEquals(
            'EVT-123456',
            $event->event_id
        );
    }

    /**
     * The same webhook event is not processed twice.
     */
    public function test_same_webhook_event_is_idempotent(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PROCESSING,
        ]);

        $firstEvent = $this->webhookService->process(
            provider: 'mtn',
            eventId: 'EVT-IDEMPOTENT',
            eventType: 'payment.success',
            payment: $payment,
            payload: [
                'status' => 'success',
            ],
            transactionReference: 'TXN-IDEMPOTENT',
        );

        $secondEvent = $this->webhookService->process(
            provider: 'mtn',
            eventId: 'EVT-IDEMPOTENT',
            eventType: 'payment.success',
            payment: $payment,
            payload: [
                'status' => 'success',
            ],
            transactionReference: 'TXN-IDEMPOTENT',
        );

        $this->assertTrue(
            $firstEvent->is($secondEvent)
        );

        $this->assertEquals(
            1,
            PaymentWebhookEvent::query()
                ->where('provider', 'mtn')
                ->where('event_id', 'EVT-IDEMPOTENT')
                ->count()
        );

        $this->assertEquals(
            PaymentStatusEnum::PAID,
            $payment->fresh()->status
        );
    }

    /**
     * A failed payment webhook marks the payment as failed.
     */
    public function test_failed_webhook_marks_payment_as_failed(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PROCESSING,
        ]);

        $event = $this->webhookService->process(
            provider: 'moov',
            eventId: 'EVT-FAILED',
            eventType: 'payment.failed',
            payment: $payment,
            payload: [
                'status' => 'failed',
            ],
        );

        $this->assertEquals(
            PaymentStatusEnum::FAILED,
            $payment->fresh()->status
        );

        $this->assertNotNull(
            $event->processed_at
        );
    }

    /**
     * Unknown webhook types must be rejected.
     */
    public function test_unknown_webhook_type_is_rejected(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PROCESSING,
        ]);

        $this->expectException(\RuntimeException::class);

        $this->webhookService->process(
            provider: 'celtis',
            eventId: 'EVT-UNKNOWN',
            eventType: 'payment.unknown',
            payment: $payment,
            payload: [
                'status' => 'unknown',
            ],
        );
    }
        /**
     * A webhook with a different amount is rejected.
     */
    public function test_webhook_with_different_amount_is_rejected(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PROCESSING,
            'amount' => 5000,
        ]);

        $this->expectException(\RuntimeException::class);

        $this->webhookService->process(
            provider: 'mtn',
            eventId: 'EVT-WRONG-AMOUNT',
            eventType: 'payment.success',
            payment: $payment,
            payload: [
                'status' => 'success',
                'amount' => 7000,
            ],
            transactionReference: 'TXN-5000',
            amount: 7000,
        );

        $this->assertEquals(
            PaymentStatusEnum::PROCESSING,
            $payment->fresh()->status
        );
    }

        /**
     * A webhook with a different transaction reference is rejected.
     */
    public function test_webhook_with_different_transaction_reference_is_rejected(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PROCESSING,
            'amount' => 5000,
            'transaction_reference' => 'TXN-ORIGINAL',
        ]);

        $this->expectException(\RuntimeException::class);

        $this->webhookService->process(
            provider: 'mtn',
            eventId: 'EVT-WRONG-REFERENCE',
            eventType: 'payment.success',
            payment: $payment,
            payload: [
                'status' => 'success',
                'amount' => 5000,
            ],
            transactionReference: 'TXN-DIFFERENT',
            amount: 5000,
        );

        $this->assertEquals(
            PaymentStatusEnum::PROCESSING,
            $payment->fresh()->status
        );
    }

    /**
     * An invalid webhook must not create a webhook event.
     */
    public function test_invalid_webhook_does_not_create_event(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PROCESSING,
            'amount' => 5000,
        ]);

        $this->expectException(\RuntimeException::class);

        $this->webhookService->process(
            provider: 'mtn',
            eventId: 'EVT-INVALID',
            eventType: 'payment.success',
            payment: $payment,
            payload: [
                'status' => 'success',
            ],
            amount: 7000,
        );

        $this->assertDatabaseMissing(
            'payment_webhook_events',
            [
                'provider' => 'mtn',
                'event_id' => 'EVT-INVALID',
            ]
        );
    }

        /**
     * A webhook event remains unprocessed when payment processing fails.
     */
    public function test_failed_payment_processing_leaves_event_unprocessed(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::FAILED,
            'transaction_reference' => 'TXN-FAILED',
        ]);

        $this->expectException(\RuntimeException::class);

        $this->webhookService->process(
            provider: 'mtn',
            eventId: 'EVT-PROCESSING-FAILED',
            eventType: 'payment.success',
            payment: $payment,
            payload: [
                'status' => 'success',
            ],
            transactionReference: 'TXN-FAILED',
        );

        $this->assertDatabaseHas(
            'payment_webhook_events',
            [
                'provider' => 'mtn',
                'event_id' => 'EVT-PROCESSING-FAILED',
                'processed_at' => null,
            ]
        );
    }

        /**
     * An unprocessed webhook event can be retried successfully.
     */
    public function test_unprocessed_webhook_event_can_be_retried(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::FAILED,
            'transaction_reference' => 'TXN-RETRY',
        ]);

        /*
        * Create an existing webhook event that has not
        * been processed yet.
        */
        $event = PaymentWebhookEvent::factory()->create([
            'payment_id' => $payment->id,
            'provider' => 'mtn',
            'event_id' => 'EVT-RETRY',
            'event_type' => 'payment.success',
            'transaction_reference' => 'TXN-RETRY',
            'payload' => [
                'status' => 'success',
            ],
            'processed_at' => null,
        ]);

        /*
        * Change the payment to a valid state for retry.
        *
        * This simulates a transient business/infrastructure
        * problem being resolved before the retry.
        */
        $payment->update([
            'status' => PaymentStatusEnum::PROCESSING,
        ]);

        $retriedEvent = $this->webhookService->process(
            provider: 'mtn',
            eventId: 'EVT-RETRY',
            eventType: 'payment.success',
            payment: $payment,
            payload: [
                'status' => 'success',
            ],
            transactionReference: 'TXN-RETRY',
        );

        $this->assertTrue(
            $event->is($retriedEvent)
        );

        $this->assertNotNull(
            $retriedEvent->processed_at
        );

        $this->assertEquals(
            PaymentStatusEnum::PAID,
            $payment->fresh()->status
        );

        $this->assertEquals(
            1,
            PaymentWebhookEvent::query()
                ->where('provider', 'mtn')
                ->where('event_id', 'EVT-RETRY')
                ->count()
        );
    }
}