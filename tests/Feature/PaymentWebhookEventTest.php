<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class PaymentWebhookEventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A webhook event belongs to a payment.
     */
    public function test_webhook_event_belongs_to_payment(): void
    {
        $payment = Payment::factory()->create();

        $event = PaymentWebhookEvent::factory()->create([
            'payment_id' => $payment->id,
        ]);

        $this->assertTrue(
            $event->payment->is($payment)
        );
    }

    /**
     * Payload is automatically cast to an array.
     */
    public function test_webhook_payload_is_cast_to_array(): void
    {
        $event = PaymentWebhookEvent::factory()->create([
            'payload' => [
                'status' => 'success',
                'amount' => 1500,
            ],
        ]);

        $this->assertIsArray($event->payload);

        $this->assertEquals(
            'success',
            $event->payload['status']
        );

        $this->assertEquals(
            1500,
            $event->payload['amount']
        );
    }

    /**
     * A payment can have multiple webhook events.
     */
    public function test_payment_can_have_multiple_webhook_events(): void
    {
        $payment = Payment::factory()->create();

        PaymentWebhookEvent::factory()
            ->count(3)
            ->create([
                'payment_id' => $payment->id,
            ]);

        $this->assertCount(
            3,
            $payment->webhookEvents
        );
    }

    /**
     * The same provider cannot register the same event twice.
     */
    public function test_same_provider_cannot_have_duplicate_event_id(): void
    {
        $event = PaymentWebhookEvent::factory()->create([
            'provider' => 'mtn',
            'event_id' => 'EVT-123456',
        ]);

        $this->expectException(QueryException::class);

        PaymentWebhookEvent::factory()->create([
            'payment_id' => $event->payment_id,
            'provider' => 'mtn',
            'event_id' => 'EVT-123456',
        ]);
    }

    /**
     * Two different providers can use the same event ID.
     */
    public function test_different_providers_can_use_same_event_id(): void
    {
        PaymentWebhookEvent::factory()->create([
            'provider' => 'mtn',
            'event_id' => 'EVT-123456',
        ]);

        $event = PaymentWebhookEvent::factory()->create([
            'provider' => 'moov',
            'event_id' => 'EVT-123456',
        ]);

        $this->assertEquals(
            'EVT-123456',
            $event->event_id
        );

        $this->assertEquals(
            'moov',
            $event->provider
        );
    }

    /**
     * A processed event stores its processed timestamp.
     */
    public function test_processed_event_has_processed_at(): void
    {
        $event = PaymentWebhookEvent::factory()
            ->processed()
            ->create();

        $this->assertNotNull(
            $event->processed_at
        );

        $this->assertInstanceOf(
            \Illuminate\Support\Carbon::class,
            $event->processed_at
        );
    }

    /**
     * An unprocessed event has no processed timestamp.
     */
    public function test_new_event_is_not_processed(): void
    {
        $event = PaymentWebhookEvent::factory()->create();

        $this->assertNull(
            $event->processed_at
        );
    }
}