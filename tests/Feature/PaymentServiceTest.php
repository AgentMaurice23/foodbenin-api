<?php

namespace Tests\Feature;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentService = app(
            PaymentService::class
        );
    }

    /**
     * Test that a pending payment can be created.
     */
    public function test_payment_can_be_created(): void
    {
        $order = Order::factory()->create([
            'total' => 15000,
            'is_paid' => false,
        ]);

        $payment = $this->paymentService->create(
            $order,
            PaymentMethodEnum::MTN
        );

        $this->assertInstanceOf(
            Payment::class,
            $payment
        );

        $this->assertSame(
            $order->id,
            $payment->order_id
        );

        $this->assertSame(
            PaymentStatusEnum::PENDING,
            $payment->status
        );

        $this->assertEquals(
            '15000.00',
            $payment->amount
        );

        $this->assertNotNull(
            $payment->uuid
        );
    }

    /**
     * Test that a payment can be initialized.
     */
    public function test_payment_can_be_initialized(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PENDING,
        ]);

        $payment = $this->paymentService->initialize(
            $payment
        );

        $this->assertSame(
            PaymentStatusEnum::PROCESSING,
            $payment->status
        );
    }

    /**
     * Test that a payment can be marked as paid.
     */
    public function test_payment_can_be_marked_as_paid(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PROCESSING,
        ]);

        $payment = $this->paymentService->markAsPaid(
            $payment,
            'MTN-TXN-123456'
        );

        $this->assertSame(
            PaymentStatusEnum::PAID,
            $payment->status
        );

        $this->assertSame(
            'MTN-TXN-123456',
            $payment->transaction_reference
        );

        $this->assertNotNull(
            $payment->paid_at
        );

        $this->assertTrue(
            $payment->order->is_paid
        );
    }

    /**
     * Test that a payment can be marked as failed.
     */
    public function test_payment_can_be_marked_as_failed(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PROCESSING,
        ]);

        $payment = $this->paymentService->markAsFailed(
            $payment
        );

        $this->assertSame(
            PaymentStatusEnum::FAILED,
            $payment->status
        );
    }

    /**
     * Test that a paid payment can be refunded.
     */
    public function test_paid_payment_can_be_refunded(): void
    {
        $payment = Payment::factory()
            ->paid()
            ->create();

        $payment->order->update([
            'is_paid' => true,
        ]);

        $payment = $this->paymentService->refund(
            $payment
        );

        $this->assertSame(
            PaymentStatusEnum::REFUNDED,
            $payment->status
        );

        $this->assertFalse(
            $payment->order->is_paid
        );
    }

    /**
     * Test that a second active payment cannot be created.
     */
    public function test_second_active_payment_cannot_be_created(): void
    {
        $order = Order::factory()->create([
            'total' => 15000,
            'is_paid' => false,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'status' => PaymentStatusEnum::PROCESSING,
        ]);

        $this->expectException(
            RuntimeException::class
        );

        $this->paymentService->create(
            $order,
            PaymentMethodEnum::MTN
        );
    }

    /**
     * Test that a paid order cannot receive another payment.
     */
    public function test_paid_order_cannot_receive_another_payment(): void
    {
        $order = Order::factory()->create([
            'total' => 15000,
            'is_paid' => true,
        ]);

        $this->expectException(
            RuntimeException::class
        );

        $this->paymentService->create(
            $order,
            PaymentMethodEnum::MTN
        );
    }

    /**
     * Test that a payment cannot be initialized twice.
     */
    public function test_payment_cannot_be_initialized_twice(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PROCESSING,
        ]);

        $this->expectException(
            RuntimeException::class
        );

        $this->paymentService->initialize(
            $payment
        );
    }

    /**
     * Test that a failed payment cannot be marked as paid.
     */
    public function test_failed_payment_cannot_be_marked_as_paid(): void
    {
        $payment = Payment::factory()->failed()->create();

        $this->expectException(
            RuntimeException::class
        );

        $this->paymentService->markAsPaid(
            $payment
        );
    }

    /**
     * Test that a refunded payment cannot be refunded again.
     */
    public function test_refunded_payment_cannot_be_refunded_again(): void
    {
        $payment = Payment::factory()
            ->refunded()
            ->create();

        $this->expectException(
            RuntimeException::class
        );

        $this->paymentService->refund(
            $payment
        );
    }

    /**
     * Un paiement déjà payé avec la même référence
     * peut être confirmé plusieurs fois sans erreur.
     */
    public function test_paid_payment_is_idempotent_with_same_transaction_reference(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PROCESSING,
            'transaction_reference' => 'TXN-123456',
        ]);

        $result = $this->paymentService->markAsPaid(
            $payment,
            'TXN-123456'
        );

        $this->assertEquals(
            PaymentStatusEnum::PAID,
            $result->status
        );

        $this->assertEquals(
            'TXN-123456',
            $result->transaction_reference
        );

        // Deuxième confirmation avec exactement la même référence.
        $resultAgain = $this->paymentService->markAsPaid(
            $result,
            'TXN-123456'
        );

        $this->assertEquals(
            PaymentStatusEnum::PAID,
            $resultAgain->status
        );

        $this->assertEquals(
            'TXN-123456',
            $resultAgain->transaction_reference
        );
    }

    /**
     * Un paiement déjà payé avec une référence différente
     * doit être rejeté afin d'éviter une incohérence.
     */
    public function test_paid_payment_cannot_be_confirmed_with_different_transaction_reference(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PAID,
            'transaction_reference' => 'TXN-123456',
        ]);

        $this->expectException(RuntimeException::class);

        $this->paymentService->markAsPaid(
            $payment,
            'TXN-999999'
        );
    }
}