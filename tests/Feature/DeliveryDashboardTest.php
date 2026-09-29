<?php

namespace Tests\Feature;

use App\Enums\DeliveryStatusEnum;
use App\Enums\DriverStatusEnum;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\Delivery\DeliveryDashboardService;


class DeliveryDashboardTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Dashboard Delivery
    |--------------------------------------------------------------------------
    */

    public function test_dashboard_returns_delivery_statistics(): void
    {
        $driver = Driver::factory()->create([
            'status' => DriverStatusEnum::ONLINE,
            'is_verified' => true,
        ]);

        Delivery::factory()->create([
            'status' => DeliveryStatusEnum::PENDING,
        ]);

        Delivery::factory()->create([
            'driver_id' => $driver->id,
            'status' => DeliveryStatusEnum::ASSIGNED,
        ]);

        Delivery::factory()->create([
            'driver_id' => $driver->id,
            'status' => DeliveryStatusEnum::DELIVERED,
        ]);

        $this->assertDatabaseCount('deliveries', 3);
    }

    public function test_dashboard_counts_deliveries_by_status(): void
    {
        Delivery::factory()->count(3)->create([
            'status' => DeliveryStatusEnum::PENDING,
        ]);

        Delivery::factory()->count(2)->create([
            'status' => DeliveryStatusEnum::ASSIGNED,
        ]);

        Delivery::factory()->create([
            'status' => DeliveryStatusEnum::DELIVERED,
        ]);

        $this->assertSame(
            3,
            Delivery::where(
                'status',
                DeliveryStatusEnum::PENDING
            )->count()
        );

        $this->assertSame(
            2,
            Delivery::where(
                'status',
                DeliveryStatusEnum::ASSIGNED
            )->count()
        );

        $this->assertSame(
            1,
            Delivery::where(
                'status',
                DeliveryStatusEnum::DELIVERED
            )->count()
        );
    }

    public function test_dashboard_counts_online_drivers(): void
    {
        Driver::factory()->create([
            'status' => DriverStatusEnum::ONLINE,
            'is_verified' => true,
        ]);

        Driver::factory()->create([
            'status' => DriverStatusEnum::OFFLINE,
            'is_verified' => true,
        ]);

        Driver::factory()->create([
            'status' => DriverStatusEnum::BUSY,
            'is_verified' => true,
        ]);

        $this->assertSame(
            1,
            Driver::where(
                'status',
                DriverStatusEnum::ONLINE
            )->count()
        );
    }

    public function test_dashboard_counts_busy_drivers(): void
    {
        Driver::factory()->create([
            'status' => DriverStatusEnum::BUSY,
            'is_verified' => true,
        ]);

        Driver::factory()->create([
            'status' => DriverStatusEnum::BUSY,
            'is_verified' => true,
        ]);

        Driver::factory()->create([
            'status' => DriverStatusEnum::ONLINE,
            'is_verified' => true,
        ]);

        $this->assertSame(
            2,
            Driver::where(
                'status',
                DriverStatusEnum::BUSY
            )->count()
        );
    }

    public function test_dashboard_can_calculate_delivery_revenue(): void
    {
        Delivery::factory()->create([
            'status' => DeliveryStatusEnum::DELIVERED,
            'delivery_fee' => 1500,
            'driver_earning' => 1000,
            'platform_commission' => 500,
        ]);

        Delivery::factory()->create([
            'status' => DeliveryStatusEnum::DELIVERED,
            'delivery_fee' => 2000,
            'driver_earning' => 1400,
            'platform_commission' => 600,
        ]);

        $this->assertSame(
            3500.0,
            (float) Delivery::where(
                'status',
                DeliveryStatusEnum::DELIVERED
            )->sum('delivery_fee')
        );

        $this->assertSame(
            1100.0,
            (float) Delivery::where(
                'status',
                DeliveryStatusEnum::DELIVERED
            )->sum('platform_commission')
        );
    }

    public function test_dashboard_service_returns_delivery_statistics(): void
    {
    Delivery::factory()->count(3)->create([
    'status' => DeliveryStatusEnum::PENDING,
    ]);

    Delivery::factory()->count(2)->create([
        'status' => DeliveryStatusEnum::ASSIGNED,
    ]);

    Delivery::factory()->create([
        'status' => DeliveryStatusEnum::DELIVERED,
        'delivery_fee' => 2000,
        'driver_earning' => 1400,
        'platform_commission' => 600,
    ]);

    $service = app(DeliveryDashboardService::class);

    $statistics = $service->getStatistics();

    $this->assertSame(
        6,
        $statistics['deliveries']['total']
    );

    $this->assertSame(
        3,
        $statistics['deliveries']['pending']
    );

    $this->assertSame(
        2,
        $statistics['deliveries']['assigned']
    );

    $this->assertSame(
        1,
        $statistics['deliveries']['delivered']
    );

    $this->assertSame(
        2000.0,
        $statistics['revenue']['delivery_fees']
    );

    $this->assertSame(
        1400.0,
        $statistics['revenue']['driver_earnings']
    );

    $this->assertSame(
        600.0,
        $statistics['revenue']['platform_commission']
    );

    }

    public function test_dashboard_service_returns_driver_statistics(): void
    {
    Driver::factory()->create([
    'status' => DriverStatusEnum::ONLINE,
    'is_verified' => true,
    ]);

    Driver::factory()->create([
        'status' => DriverStatusEnum::BUSY,
        'is_verified' => true,
    ]);

    Driver::factory()->create([
        'status' => DriverStatusEnum::OFFLINE,
        'is_verified' => false,
    ]);

    $service = app(DeliveryDashboardService::class);

    $statistics = $service->getStatistics();

    $this->assertSame(
        3,
        $statistics['drivers']['total']
    );

    $this->assertSame(
        1,
        $statistics['drivers']['online']
    );

    $this->assertSame(
        1,
        $statistics['drivers']['busy']
    );

    $this->assertSame(
        1,
        $statistics['drivers']['offline']
    );

    $this->assertSame(
        2,
        $statistics['drivers']['verified']
    );

    $this->assertSame(
        1,
        $statistics['drivers']['unverified']
    );

    }

}
