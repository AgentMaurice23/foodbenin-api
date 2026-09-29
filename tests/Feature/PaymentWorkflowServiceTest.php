<?php

namespace Tests\Feature;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use App\Services\Payment\PaymentWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class PaymentWorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PENDING payment can transition to PROCESSING.
     */
    public function test_pending_payment_can_be_initialized(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PENDING,
        ]);

        $service = new PaymentWorkflowService();

        $payment = $service->initialize($payment);

        $this->assertSame(
            PaymentStatusEnum::PROCESSING,
            $payment->status
        );
    }

    /**
     * PROCESSING payment can transition to PAID.
     */
    public function test_processing_payment_can_be_marked_as_paid(): void
    {
        $payment = Payment::factory()->processing()->create();

        $service = new PaymentWorkflowService();

        $payment = $service->markAsPaid(
            $payment,
            'TX-123456'
        );

        $this->assertSame(
            PaymentStatusEnum::PAID,
            $payment->status
        );

        $this->assertSame(
            'TX-123456',
            $payment->transaction_reference
        );

        $this->assertNotNull(
            $payment->paid_at
        );
    }

    /**
     * PENDING payment can transition directly to PAID.
     *
     * This is useful for providers that confirm
     * a payment immediately.
     */
    public function test_pending_payment_can_be_marked_as_paid(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PENDING,
        ]);

        $service = new PaymentWorkflowService();

        $payment = $service->markAsPaid(
            $payment,
            'TX-987654'
        );

        $this->assertSame(
            PaymentStatusEnum::PAID,
            $payment->status
        );
    }

    /**
     * PROCESSING payment can transition to FAILED.
     */
    public function test_processing_payment_can_fail(): void
    {
        $payment = Payment::factory()->processing()->create();

        $service = new PaymentWorkflowService();

        $payment = $service->fail($payment);

        $this->assertSame(
            PaymentStatusEnum::FAILED,
            $payment->status
        );
    }

    /**
     * PAID payment can transition to REFUNDED.
     */
    public function test_paid_payment_can_be_refunded(): void
    {
        $payment = Payment::factory()->paid()->create();

        $service = new PaymentWorkflowService();

        $payment = $service->refund($payment);

        $this->assertSame(
            PaymentStatusEnum::REFUNDED,
            $payment->status
        );
    }

    /**
     * A PAID payment cannot transition to FAILED.
     */
    public function test_paid_payment_cannot_fail(): void
    {
        $payment = Payment::factory()->paid()->create();

        $service = new PaymentWorkflowService();

        $this->expectException(RuntimeException::class);

        $service->fail($payment);
    }

    /**
     * A REFUNDED payment cannot transition to PAID.
     */
    public function test_refunded_payment_cannot_be_paid_again(): void
    {
        $payment = Payment::factory()->refunded()->create();

        $service = new PaymentWorkflowService();

        $this->expectException(RuntimeException::class);

        $service->markAsPaid($payment);
    }

    /**
     * A REFUNDED payment cannot be refunded twice.
     */
    public function test_refunded_payment_cannot_be_refunded_again(): void
    {
        $payment = Payment::factory()->refunded()->create();

        $service = new PaymentWorkflowService();

        $this->expectException(RuntimeException::class);

        $service->refund($payment);
    }

    /**
     * A FAILED payment cannot be paid again.
     */
    public function test_failed_payment_cannot_be_paid_again(): void
    {
        $payment = Payment::factory()->failed()->create();

        $service = new PaymentWorkflowService();

        $this->expectException(RuntimeException::class);

        $service->markAsPaid($payment);
    }

    /**
     * Verify the allowed payment transitions.
     */
    public function test_payment_transition_rules_are_correct(): void
    {
        $service = new PaymentWorkflowService();

        $pending = Payment::factory()->create([
            'status' => PaymentStatusEnum::PENDING,
        ]);

        $processing = Payment::factory()->processing()->create();

        $paid = Payment::factory()->paid()->create();

        $failed = Payment::factory()->failed()->create();

        $refunded = Payment::factory()->refunded()->create();

        $this->assertTrue(
            $service->canTransitionTo(
                $pending,
                PaymentStatusEnum::PROCESSING
            )
        );

        $this->assertTrue(
            $service->canTransitionTo(
                $pending,
                PaymentStatusEnum::PAID
            )
        );

        $this->assertTrue(
            $service->canTransitionTo(
                $processing,
                PaymentStatusEnum::PAID
            )
        );

        $this->assertTrue(
            $service->canTransitionTo(
                $processing,
                PaymentStatusEnum::FAILED
            )
        );

        $this->assertTrue(
            $service->canTransitionTo(
                $paid,
                PaymentStatusEnum::REFUNDED
            )
        );

        $this->assertFalse(
            $service->canTransitionTo(
                $paid,
                PaymentStatusEnum::FAILED
            )
        );

        $this->assertFalse(
            $service->canTransitionTo(
                $failed,
                PaymentStatusEnum::PAID
            )
        );

        $this->assertFalse(
            $service->canTransitionTo(
                $refunded,
                PaymentStatusEnum::PAID
            )
        );
    }
}