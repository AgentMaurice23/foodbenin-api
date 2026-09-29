<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Services\Payment\Webhook\PaymentWebhookPayload;
use App\Services\Payment\Webhook\PaymentWebhookSecurityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class PaymentWebhookSecurityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentWebhookSecurityService $securityService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->securityService = app(
            PaymentWebhookSecurityService::class
        );
    }

    /**
     * An authorized provider is accepted.
     */
    public function test_authorized_provider_is_accepted(): void
    {
        $this->securityService->validateProvider('mtn');

        $this->assertTrue(true);
    }

    /**
     * An unknown provider is rejected.
     */
    public function test_unknown_provider_is_rejected(): void
    {
        $this->expectException(RuntimeException::class);

        $this->securityService->validateProvider(
            'unknown_provider'
        );
    }

    /**
     * An empty event ID is rejected.
     */
    public function test_empty_event_id_is_rejected(): void
    {
        $this->expectException(RuntimeException::class);

        $this->securityService->validateEventId('');
    }

    /**
     * An unsupported event type is rejected.
     */
    public function test_unknown_event_type_is_rejected(): void
    {
        $this->expectException(RuntimeException::class);

        $this->securityService->validateEventType(
            'payment.unknown'
        );
    }

    /**
     * A matching amount is accepted.
     */
    public function test_matching_amount_is_accepted(): void
    {
        $payment = Payment::factory()->create([
            'amount' => 1500,
        ]);

        $this->securityService->validateAmount(
            $payment,
            1500
        );

        $this->assertTrue(true);
    }

    /**
     * A different amount is rejected.
     */
    public function test_different_amount_is_rejected(): void
    {
        $payment = Payment::factory()->create([
            'amount' => 1500,
        ]);

        $this->expectException(RuntimeException::class);

        $this->securityService->validateAmount(
            $payment,
            2000
        );
    }

    /**
     * A matching transaction reference is accepted.
     */
    public function test_matching_transaction_reference_is_accepted(): void
    {
        $payment = Payment::factory()->create([
            'transaction_reference' => 'TXN-123',
        ]);

        $this->securityService->validateTransactionReference(
            $payment,
            'TXN-123'
        );

        $this->assertTrue(true);
    }

    /**
     * A different transaction reference is rejected.
     */
    public function test_different_transaction_reference_is_rejected(): void
    {
        $payment = Payment::factory()->create([
            'transaction_reference' => 'TXN-123',
        ]);

        $this->expectException(RuntimeException::class);

        $this->securityService->validateTransactionReference(
            $payment,
            'TXN-999'
        );
    }

    /**
     * A valid normalized webhook payload passes security validation.
     */
    public function test_valid_webhook_payload_passes_validation(): void
    {
        $payment = Payment::factory()->create([
            'amount' => 1500,
            'transaction_reference' => 'TXN-123',
        ]);

        $webhook = new PaymentWebhookPayload(
            provider: 'mtn',
            eventId: 'EVT-123',
            eventType: 'payment.success',
            payment: $payment,
            payload: [
                'status' => 'success',
            ],
            transactionReference: 'TXN-123',
            amount: 1500,
        );

        $this->securityService->validate(
            $webhook
        );

        $this->assertTrue(true);
    }

    /**
     * An invalid amount prevents complete webhook validation.
     */
    public function test_invalid_amount_prevents_webhook_validation(): void
    {
        $payment = Payment::factory()->create([
            'amount' => 1500,
            'transaction_reference' => 'TXN-123',
        ]);

        $webhook = new PaymentWebhookPayload(
            provider: 'mtn',
            eventId: 'EVT-123',
            eventType: 'payment.success',
            payment: $payment,
            payload: [
                'status' => 'success',
            ],
            transactionReference: 'TXN-123',
            amount: 2000,
        );

        $this->expectException(RuntimeException::class);

        $this->securityService->validate(
            $webhook
        );
    }
}