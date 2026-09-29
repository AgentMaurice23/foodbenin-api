<?php

namespace Tests\Feature;

use App\Enums\PaymentMethodEnum;
use App\Services\Payment\PaymentProviderResolver;
use App\Services\Payment\Providers\CeltisPaymentProvider;
use App\Services\Payment\Providers\MtnPaymentProvider;
use App\Services\Payment\Providers\MoovPaymentProvider;
use Tests\TestCase;

class PaymentProviderResolverTest extends TestCase
{
    /**
     * The resolver returns the MTN provider.
     */
    public function test_resolver_returns_provider_for_mtn(): void
    {
        $resolver = new PaymentProviderResolver();

        $provider = $resolver->resolve(
            PaymentMethodEnum::MTN
        );

        $this->assertInstanceOf(
            MtnPaymentProvider::class,
            $provider
        );
    }

    /**
     * The resolver returns the Moov provider.
     */
    public function test_resolver_returns_provider_for_moov(): void
    {
        $resolver = new PaymentProviderResolver();

        $provider = $resolver->resolve(
            PaymentMethodEnum::MOOV
        );

        $this->assertInstanceOf(
            MoovPaymentProvider::class,
            $provider
        );
    }

    /**
     * The resolver returns the Celtis provider.
     */
    public function test_resolver_returns_provider_for_celtis(): void
    {
        $resolver = new PaymentProviderResolver();

        $provider = $resolver->resolve(
            PaymentMethodEnum::CELTIS
        );

        $this->assertInstanceOf(
            CeltisPaymentProvider::class,
            $provider
        );
    }
}