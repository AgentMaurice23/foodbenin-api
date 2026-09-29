<?php

namespace Tests\Feature;

use App\Enums\DriverStatusEnum;
use App\Enums\VehicleTypeEnum;
use App\Models\Driver;
use App\Models\DriverLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DriverTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | User → Driver
    |--------------------------------------------------------------------------
    |
    | Vérifie qu'un utilisateur possède bien un seul
    | profil Driver.
    |
    */

    public function test_user_can_have_one_driver(): void
    {
        $user = User::factory()->create();

        $driver = Driver::create([

            'user_id' => $user->id,

            'status' =>
                DriverStatusEnum::ONLINE,

            'vehicle_type' =>
                VehicleTypeEnum::MOTORBIKE,
        ]);

        $this->assertTrue(
            $user->driver->is($driver)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Driver → User
    |--------------------------------------------------------------------------
    |
    | Vérifie qu'un Driver appartient bien à un User.
    |
    */

    public function test_driver_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $driver = Driver::create([

            'user_id' => $user->id,

            'status' =>
                DriverStatusEnum::ONLINE,

            'vehicle_type' =>
                VehicleTypeEnum::MOTORBIKE,
        ]);

        $this->assertTrue(
            $driver->user->is($user)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Driver → Deliveries
    |--------------------------------------------------------------------------
    |
    | Vérifie qu'un Driver peut posséder plusieurs
    | livraisons.
    |
    */

    public function test_driver_can_have_multiple_deliveries(): void
    {
        $user = User::factory()->create();

        $driver = Driver::create([

            'user_id' => $user->id,

            'status' =>
                DriverStatusEnum::ONLINE,

            'vehicle_type' =>
                VehicleTypeEnum::MOTORBIKE,
        ]);

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $driver->deliveries
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Driver → Locations
    |--------------------------------------------------------------------------
    |
    | Vérifie qu'un Driver possède bien une relation
    | vers son historique de positions GPS.
    |
    */

    public function test_driver_has_locations(): void
    {
        $user = User::factory()->create();

        $driver = Driver::create([

            'user_id' => $user->id,

            'status' =>
                DriverStatusEnum::ONLINE,

            'vehicle_type' =>
                VehicleTypeEnum::MOTORBIKE,
        ]);

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $driver->locations
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UUID automatique
    |--------------------------------------------------------------------------
    |
    | Vérifie que le Driver reçoit automatiquement
    | un UUID lors de sa création.
    |
    */

    public function test_driver_generates_uuid_automatically(): void
    {
        $user = User::factory()->create();

        $driver = Driver::create([

            'user_id' => $user->id,

            'status' =>
                DriverStatusEnum::ONLINE,

            'vehicle_type' =>
                VehicleTypeEnum::MOTORBIKE,
        ]);

        $this->assertNotNull(
            $driver->uuid
        );

        $this->assertTrue(
            Str::isUuid($driver->uuid)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Status Enum
    |--------------------------------------------------------------------------
    |
    | Vérifie que Laravel transforme automatiquement
    | la valeur de status en DriverStatusEnum.
    |
    */

    public function test_driver_status_is_cast_to_enum(): void
    {
        $user = User::factory()->create();

        $driver = Driver::create([

            'user_id' => $user->id,

            'status' =>
                DriverStatusEnum::ONLINE,

            'vehicle_type' =>
                VehicleTypeEnum::MOTORBIKE,
        ]);

        $driver->refresh();

        $this->assertInstanceOf(
            DriverStatusEnum::class,
            $driver->status
        );

        $this->assertSame(
            DriverStatusEnum::ONLINE,
            $driver->status
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Vehicle Type Enum
    |--------------------------------------------------------------------------
    |
    | Vérifie que Laravel transforme automatiquement
    | vehicle_type en VehicleTypeEnum.
    |
    */

    public function test_driver_vehicle_type_is_cast_to_enum(): void
    {
        $user = User::factory()->create();

        $driver = Driver::create([

            'user_id' => $user->id,

            'status' =>
                DriverStatusEnum::ONLINE,

            'vehicle_type' =>
                VehicleTypeEnum::MOTORBIKE,
        ]);

        $driver->refresh();

        $this->assertInstanceOf(
            VehicleTypeEnum::class,
            $driver->vehicle_type
        );

        $this->assertSame(
            VehicleTypeEnum::MOTORBIKE,
            $driver->vehicle_type
        );
    }
}