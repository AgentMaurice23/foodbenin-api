<?php

namespace Tests\Feature;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a payment belongs to an order.
     */
    public function test_payment_belongs_to_order(): void
    {
        $payment = Payment::factory()->create();

        $this->assertInstanceOf(
            Order::class,
            $payment->order
        );

        $this->assertEquals(
            $payment->order_id,
            $payment->order->id
        );
    }

    /**
     * Test that a payment belongs to a user.
     */
    public function test_payment_belongs_to_user(): void
    {
        $payment = Payment::factory()->create();

        $this->assertInstanceOf(
            User::class,
            $payment->user
        );

        $this->assertEquals(
            $payment->user_id,
            $payment->user->id
        );
    }

    /**
     * Test that a payment belongs to a restaurant.
     */
    public function test_payment_belongs_to_restaurant(): void
    {
        $payment = Payment::factory()->create();

        $this->assertInstanceOf(
            Restaurant::class,
            $payment->restaurant
        );

        $this->assertEquals(
            $payment->restaurant_id,
            $payment->restaurant->id
        );
    }

    /**
     * Test that payment method is cast to enum.
     */
    public function test_payment_method_is_cast_to_enum(): void
    {
        $payment = Payment::factory()->create([
            'payment_method' => PaymentMethodEnum::MTN,
        ]);

        $this->assertInstanceOf(
            PaymentMethodEnum::class,
            $payment->payment_method
        );

        $this->assertSame(
            PaymentMethodEnum::MTN,
            $payment->payment_method
        );
    }

    /**
     * Test that payment status is cast to enum.
     */
    public function test_payment_status_is_cast_to_enum(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatusEnum::PENDING,
        ]);

        $this->assertInstanceOf(
            PaymentStatusEnum::class,
            $payment->status
        );

        $this->assertSame(
            PaymentStatusEnum::PENDING,
            $payment->status
        );
    }

    /**
     * Test that paid payments contain a paid date.
     */
    public function test_paid_payment_has_paid_at_date(): void
    {
        $payment = Payment::factory()
            ->paid()
            ->create();

        $this->assertSame(
            PaymentStatusEnum::PAID,
            $payment->status
        );

        $this->assertNotNull(
            $payment->paid_at
        );
    }

    /**
     * Test that payment amount is stored correctly.
     */
    public function test_payment_amount_is_stored_correctly(): void
    {
        $payment = Payment::factory()->create([
            'amount' => 12500.50,
        ]);

        $this->assertEquals(
            '12500.50',
            $payment->amount
        );
    }

    /**
     * Test that order has one payment.
     */
    public function test_order_has_one_payment(): void
    {
        $order = Order::factory()->create();

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
        ]);

        $this->assertTrue(
            $order->payment->is($payment)
        );
    }
}