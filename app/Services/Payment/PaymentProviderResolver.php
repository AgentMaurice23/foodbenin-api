<?php

namespace App\Services\Payment;

use App\Enums\PaymentMethodEnum;
use App\Services\Payment\Contracts\PaymentProviderInterface;
use App\Services\Payment\Providers\CeltisPaymentProvider;
use App\Services\Payment\Providers\FakePaymentProvider;
use App\Services\Payment\Providers\MtnPaymentProvider;
use App\Services\Payment\Providers\MoovPaymentProvider;
use InvalidArgumentException;

/**
 * Resolves the payment provider associated
 * with a payment method.
 */
class PaymentProviderResolver
{
    /**
     * Resolve a payment provider.
     *
     * @throws InvalidArgumentException
     */
    public function resolve(
        PaymentMethodEnum $paymentMethod
    ): PaymentProviderInterface {
        return match ($paymentMethod) {
            PaymentMethodEnum::MTN =>
                new MtnPaymentProvider(),

            PaymentMethodEnum::MOOV =>
                new MoovPaymentProvider(),

            PaymentMethodEnum::CELTIS =>
                new CeltisPaymentProvider(),

            PaymentMethodEnum::CARD,
            PaymentMethodEnum::CASH =>
                new FakePaymentProvider(),

            default => throw new InvalidArgumentException(
                'Aucun provider de paiement disponible pour cette méthode.'
            ),
        };
    }
}