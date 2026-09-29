<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Services\Delivery\DeliveryPricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class DeliveryPricingTest extends TestCase
{
    use RefreshDatabase;

    protected DeliveryPricingService $pricingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pricingService =
            app(DeliveryPricingService::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Commission plateforme
    |--------------------------------------------------------------------------
    */

    public function test_platform_commission_is_calculated(): void
    {
        $commission =
            $this->pricingService
                ->calculatePlatformCommission(
                    1000
                );

        $this->assertEquals(
            200.00,
            $commission
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Gain livreur
    |--------------------------------------------------------------------------
    */

    public function test_driver_earning_is_calculated(): void
    {
        $earning =
            $this->pricingService
                ->calculateDriverEarning(
                    1000
                );

        $this->assertEquals(
            800.00,
            $earning
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Pricing complet
    |--------------------------------------------------------------------------
    */

    public function test_complete_pricing_is_calculated(): void
    {
        $pricing =
            $this->pricingService
                ->calculate(
                    1000
                );

        $this->assertEquals(
            1000.00,
            $pricing['delivery_fee']
        );

        $this->assertEquals(
            20.00,
            $pricing['commission_rate']
        );

        $this->assertEquals(
            800.00,
            $pricing['driver_earning']
        );

        $this->assertEquals(
            200.00,
            $pricing['platform_commission']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cohérence financière
    |--------------------------------------------------------------------------
    */

    public function test_driver_earning_plus_platform_commission_equals_delivery_fee(): void
    {
        $pricing =
            $this->pricingService
                ->calculate(
                    2500
                );

        $this->assertEquals(
            $pricing['delivery_fee'],
            $pricing['driver_earning']
            + $pricing['platform_commission']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Taux personnalisé
    |--------------------------------------------------------------------------
    */

    public function test_custom_commission_rate_is_supported(): void
    {
        $pricing =
            $this->pricingService
                ->calculate(
                    1000,
                    30
                );

        $this->assertEquals(
            300.00,
            $pricing['platform_commission']
        );

        $this->assertEquals(
            700.00,
            $pricing['driver_earning']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Commission 0 %
    |--------------------------------------------------------------------------
    */

    public function test_zero_commission_gives_all_delivery_fee_to_driver(): void
    {
        $pricing =
            $this->pricingService
                ->calculate(
                    1000,
                    0
                );

        $this->assertEquals(
            0.00,
            $pricing['platform_commission']
        );

        $this->assertEquals(
            1000.00,
            $pricing['driver_earning']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Commission 100 %
    |--------------------------------------------------------------------------
    */

    public function test_hundred_percent_commission_gives_nothing_to_driver(): void
    {
        $pricing =
            $this->pricingService
                ->calculate(
                    1000,
                    100
                );

        $this->assertEquals(
            1000.00,
            $pricing['platform_commission']
        );

        $this->assertEquals(
            0.00,
            $pricing['driver_earning']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Frais négatifs
    |--------------------------------------------------------------------------
    */

    public function test_negative_delivery_fee_is_rejected(): void
    {
        $this->expectException(
            RuntimeException::class
        );

        $this->pricingService
            ->calculate(
                -100
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Commission invalide
    |--------------------------------------------------------------------------
    */

    public function test_invalid_commission_rate_is_rejected(): void
    {
        $this->expectException(
            RuntimeException::class
        );

        $this->pricingService
            ->calculate(
                1000,
                101
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Application du pricing à une livraison
    |--------------------------------------------------------------------------
    */

    public function test_pricing_can_be_applied_to_delivery(): void
    {
        $delivery =
            Delivery::factory()->create([
                'delivery_fee' => 1000,
                'driver_earning' => 0,
                'platform_commission' => 0,
            ]);

        $delivery =
            $this->pricingService
                ->applyToDelivery(
                    $delivery
                );

        $this->assertEquals(
            800.00,
            (float) $delivery->driver_earning
        );

        $this->assertEquals(
            200.00,
            (float) $delivery->platform_commission
        );
    }
}
