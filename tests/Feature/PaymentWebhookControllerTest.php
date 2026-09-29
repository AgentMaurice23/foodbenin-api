<?php

namespace Tests\Feature;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A successful payment webhook is processed through HTTP.
     */
    public function test_successful_payment_webhook_is_processed(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PROCESSING,
            'amount' => 5000,
            'transaction_reference' => null,
        ]);

        $response = $this->postJson(
            '/api/v1/webhooks/payment',
            [
                'provider' => 'mtn',
                'event_id' => 'EVT-HTTP-001',
                'event_type' => 'payment.success',
                'payment_id' => $payment->id,
                'transaction_reference' => 'TXN-HTTP-001',
                'amount' => 5000,
                'payload' => [
                    'status' => 'success',
                ],
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'event_id' => 'EVT-HTTP-001',
                    'provider' => 'mtn',
                    'processed' => true,
                ],
            ]);

        $this->assertEquals(
            PaymentStatusEnum::PAID,
            $payment->fresh()->status
        );

        $this->assertDatabaseHas(
            'payment_webhook_events',
            [
                'provider' => 'mtn',
                'event_id' => 'EVT-HTTP-001',
            ]
        );
    }

    /**
     * Invalid webhook data is rejected by the request validation.
     */
    public function test_invalid_webhook_request_is_rejected(): void
    {
        $response = $this->postJson(
            '/api/v1/webhooks/payment',
            []
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'provider',
                'event_id',
                'event_type',
                'payment_id',
                'payload',
            ]);
    }

    /**
     * A webhook referencing an unknown payment is rejected.
     */
    public function test_unknown_payment_is_rejected(): void
    {
        $response = $this->postJson(
            '/api/v1/webhooks/payment',
            [
                'provider' => 'mtn',
                'event_id' => 'EVT-HTTP-UNKNOWN',
                'event_type' => 'payment.success',
                'payment_id' => 999999,
                'transaction_reference' => 'TXN-UNKNOWN',
                'amount' => 5000,
                'payload' => [
                    'status' => 'success',
                ],
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'payment_id',
            ]);
    }

    /**
     * The same webhook remains idempotent through HTTP.
     */
    public function test_same_webhook_is_idempotent_through_http(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PROCESSING,
            'amount' => 5000,
        ]);

        $payload = [
            'provider' => 'mtn',
            'event_id' => 'EVT-HTTP-IDEMPOTENT',
            'event_type' => 'payment.success',
            'payment_id' => $payment->id,
            'transaction_reference' => 'TXN-HTTP-IDEMPOTENT',
            'amount' => 5000,
            'payload' => [
                'status' => 'success',
            ],
        ];

        $this->postJson(
            '/api/v1/webhooks/payment',
            $payload
        )->assertOk();

        $this->postJson(
            '/api/v1/webhooks/payment',
            $payload
        )->assertOk();

        $this->assertEquals(
            1,
            PaymentWebhookEvent::query()
                ->where('provider', 'mtn')
                ->where(
                    'event_id',
                    'EVT-HTTP-IDEMPOTENT'
                )
                ->count()
        );

        $this->assertEquals(
            PaymentStatusEnum::PAID,
            $payment->fresh()->status
        );
    }
}