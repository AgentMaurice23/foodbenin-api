<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Services\Payment\Providers\FakePaymentProvider;
use App\Services\Payment\Providers\CeltisPaymentProvider;
use App\Services\Payment\Providers\MtnPaymentProvider;
use App\Services\Payment\Providers\MoovPaymentProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentProviderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The fake provider can initialize a payment.
     */
    public function test_fake_provider_can_initialize_payment(): void
    {
        $payment = Payment::factory()->create();

        $provider = new FakePaymentProvider();

        $result = $provider->initializePayment($payment);

        $this->assertTrue($result->success);
        $this->assertSame('processing', $result->status);
        $this->assertNotNull(
            $result->transactionReference
        );
    }

    /**
     * The fake provider can check payment status.
     */
    public function test_fake_provider_can_check_status(): void
    {
        $payment = Payment::factory()->create();

        $provider = new FakePaymentProvider();

        $result = $provider->checkStatus($payment);

        $this->assertTrue($result->success);
        $this->assertSame(
            $payment->status->value,
            $result->status
        );
    }

    /**
     * The fake provider can verify a payment.
     */
    public function test_fake_provider_can_verify_payment(): void
    {
        $payment = Payment::factory()->create();

        $provider = new FakePaymentProvider();

        $result = $provider->verifyPayment($payment);

        $this->assertTrue($result->success);
        $this->assertSame('paid', $result->status);
        $this->assertNotNull(
            $result->transactionReference
        );
    }

    /**
     * The fake provider can refund a payment.
     */
    public function test_fake_provider_can_refund_payment(): void
    {
        $payment = Payment::factory()->paid()->create();

        $provider = new FakePaymentProvider();

        $result = $provider->refund($payment);

        $this->assertTrue($result->success);
        $this->assertSame('refunded', $result->status);
    }
}