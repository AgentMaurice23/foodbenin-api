<?php

namespace App\Services\Delivery;

use App\Models\Delivery;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DeliveryPricingService
{
    /*
    |--------------------------------------------------------------------------
    | Taux de commission par défaut
    |--------------------------------------------------------------------------
    |
    | 20 % de la valeur des frais de livraison reviennent à la plateforme.
    | 80 % reviennent au livreur.
    |
    */

    private const DEFAULT_COMMISSION_RATE = 20.0;

    /*
    |--------------------------------------------------------------------------
    | Calculer la commission plateforme
    |--------------------------------------------------------------------------
    */

    public function calculatePlatformCommission(
        float $deliveryFee,
        ?float $commissionRate = null
    ): float {

        $this->validateDeliveryFee($deliveryFee);

        $commissionRate ??=
            self::DEFAULT_COMMISSION_RATE;

        $this->validateCommissionRate(
            $commissionRate
        );

        return round(
            $deliveryFee * ($commissionRate / 100),
            2
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Calculer le gain du livreur
    |--------------------------------------------------------------------------
    */

    public function calculateDriverEarning(
        float $deliveryFee,
        ?float $commissionRate = null
    ): float {

        $this->validateDeliveryFee($deliveryFee);

        $commission =
            $this->calculatePlatformCommission(
                $deliveryFee,
                $commissionRate
            );

        return round(
            $deliveryFee - $commission,
            2
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Calculer l'ensemble du pricing
    |--------------------------------------------------------------------------
    */

    public function calculate(
        float $deliveryFee,
        ?float $commissionRate = null
    ): array {

        $this->validateDeliveryFee($deliveryFee);

        $commissionRate ??=
            self::DEFAULT_COMMISSION_RATE;

        $this->validateCommissionRate(
            $commissionRate
        );

        $platformCommission =
            $this->calculatePlatformCommission(
                $deliveryFee,
                $commissionRate
            );

        $driverEarning =
            round(
                $deliveryFee - $platformCommission,
                2
            );

        return [

            'delivery_fee' =>
                round($deliveryFee, 2),

            'commission_rate' =>
                $commissionRate,

            'driver_earning' =>
                $driverEarning,

            'platform_commission' =>
                $platformCommission,

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Appliquer le pricing à une livraison
    |--------------------------------------------------------------------------
    */

    public function applyToDelivery(
        Delivery $delivery,
        ?float $commissionRate = null
    ): Delivery {

        return DB::transaction(
            function () use (
                $delivery,
                $commissionRate
            ) {

                $delivery =
                    Delivery::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $delivery->id
                        );

                $pricing =
                    $this->calculate(
                        (float) $delivery->delivery_fee,
                        $commissionRate
                    );

                $delivery->driver_earning =
                    $pricing['driver_earning'];

                $delivery->platform_commission =
                    $pricing['platform_commission'];

                $delivery->save();

                return $delivery->fresh();
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validation des frais de livraison
    |--------------------------------------------------------------------------
    */

    protected function validateDeliveryFee(
        float $deliveryFee
    ): void {

        if ($deliveryFee < 0) {
            throw new RuntimeException(
                'Les frais de livraison ne peuvent pas être négatifs.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Validation du taux de commission
    |--------------------------------------------------------------------------
    */

    protected function validateCommissionRate(
        float $commissionRate
    ): void {

        if (
            $commissionRate < 0 ||
            $commissionRate > 100
        ) {
            throw new RuntimeException(
                'Le taux de commission doit être compris entre 0 et 100.'
            );
        }
    }
}
