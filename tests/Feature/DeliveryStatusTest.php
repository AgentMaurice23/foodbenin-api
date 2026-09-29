<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Enums\DeliveryStatusEnum;

class DeliveryStatusTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Vérifie la transition PENDING → ASSIGNED.
    |--------------------------------------------------------------------------
    */

    public function test_pending_can_become_assigned(): void
    {
        $this->assertTrue(

            DeliveryStatusEnum::PENDING
                ->canTransitionTo(
                    DeliveryStatusEnum::ASSIGNED
                )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Vérifie la transition ASSIGNED → ACCEPTED.
    |--------------------------------------------------------------------------
    */

    public function test_assigned_can_become_accepted(): void
    {
        $this->assertTrue(

            DeliveryStatusEnum::ASSIGNED
                ->canTransitionTo(
                    DeliveryStatusEnum::ACCEPTED
                )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Une livraison ne peut pas passer directement
    | de PENDING à DELIVERED.
    |--------------------------------------------------------------------------
    */

    public function test_pending_cannot_become_delivered(): void
    {
        $this->assertFalse(

            DeliveryStatusEnum::PENDING
                ->canTransitionTo(
                    DeliveryStatusEnum::DELIVERED
                )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Une livraison terminée est un état final.
    |--------------------------------------------------------------------------
    */

    public function test_delivered_is_final(): void
    {
        $this->assertTrue(

            DeliveryStatusEnum::DELIVERED
                ->isFinal()
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Une livraison annulée est un état final.
    |--------------------------------------------------------------------------
    */

    public function test_cancelled_is_final(): void
    {
        $this->assertTrue(

            DeliveryStatusEnum::CANCELLED
                ->isFinal()
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Une livraison échouée est un état final.
    |--------------------------------------------------------------------------
    */

    public function test_failed_is_final(): void
    {
        $this->assertTrue(

            DeliveryStatusEnum::FAILED
                ->isFinal()
        );
    }
}
