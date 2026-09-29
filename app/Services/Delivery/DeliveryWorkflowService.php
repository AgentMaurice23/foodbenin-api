<?php

namespace App\Services\Delivery;

use App\Enums\DeliveryStatusEnum;
use App\Enums\DriverStatusEnum;
use App\Models\Delivery;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DeliveryWorkflowService
{
    public function __construct(
        protected DeliveryService $deliveryService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Accepter une livraison
    |--------------------------------------------------------------------------
    */

    public function accept(Delivery $delivery): Delivery
    {
        return DB::transaction(function () use ($delivery) {

            $delivery = Delivery::query()
                ->with('driver')
                ->lockForUpdate()
                ->findOrFail($delivery->id);

            if (!$delivery->driver) {
                throw new RuntimeException(
                    'Cette livraison n’a aucun livreur assigné.'
                );
            }

            if (
                $delivery->status !==
                DeliveryStatusEnum::ASSIGNED
            ) {
                throw new RuntimeException(
                    'Cette livraison ne peut pas être acceptée.'
                );
            }

            $this->deliveryService->updateStatus(
                $delivery,
                DeliveryStatusEnum::ACCEPTED
            );

            return $delivery->fresh([
                'driver',
                'order',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Refuser une livraison
    |--------------------------------------------------------------------------
    */

    public function reject(
        Delivery $delivery,
        string $reason
    ): Delivery {

        return DB::transaction(function () use (
            $delivery,
            $reason
        ) {

            $delivery = Delivery::query()
                ->with('driver')
                ->lockForUpdate()
                ->findOrFail($delivery->id);

            if (
                $delivery->status !==
                DeliveryStatusEnum::ASSIGNED
            ) {
                throw new RuntimeException(
                    'Cette livraison ne peut pas être refusée.'
                );
            }

            $driver = $delivery->driver;

            if (!$driver) {
                throw new RuntimeException(
                    'Cette livraison n’a aucun livreur assigné.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Libérer le livreur
            |--------------------------------------------------------------------------
            */

            $driver->status =
                DriverStatusEnum::ONLINE;

            $driver->save();

            /*
            |--------------------------------------------------------------------------
            | Remettre la livraison en attente
            |--------------------------------------------------------------------------
            */

            $delivery->driver_id = null;

            $delivery->status =
                DeliveryStatusEnum::PENDING;

            $delivery->assigned_at = null;

            $delivery->cancel_reason = null;

            $delivery->save();

            /*
            |--------------------------------------------------------------------------
            | Historique
            |--------------------------------------------------------------------------
            */

            $delivery->histories()->create([
                'status' =>
                    DeliveryStatusEnum::PENDING->value,
            ]);

            return $delivery->fresh([
                'driver',
                'histories',
                'order',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Se rendre au restaurant
    |--------------------------------------------------------------------------
    */

    public function goToRestaurant(
        Delivery $delivery
    ): Delivery {

        return $this->transition(
            $delivery,
            DeliveryStatusEnum::GOING_TO_RESTAURANT
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Arrivée au restaurant
    |--------------------------------------------------------------------------
    */

    public function arriveAtRestaurant(
        Delivery $delivery
    ): Delivery {

        return $this->transition(
            $delivery,
            DeliveryStatusEnum::AT_RESTAURANT
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Récupérer la commande
    |--------------------------------------------------------------------------
    */

    public function pickup(
        Delivery $delivery
    ): Delivery {

        return $this->transition(
            $delivery,
            DeliveryStatusEnum::PICKED_UP
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Commencer la livraison
    |--------------------------------------------------------------------------
    */

    public function startDelivery(
        Delivery $delivery
    ): Delivery {

        return $this->transition(
            $delivery,
            DeliveryStatusEnum::ON_THE_WAY
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Arrivée chez le client
    |--------------------------------------------------------------------------
    */

    public function arrive(
        Delivery $delivery
    ): Delivery {

        return $this->transition(
            $delivery,
            DeliveryStatusEnum::ARRIVED
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Livraison terminée
    |--------------------------------------------------------------------------
    */

    public function complete(
        Delivery $delivery
    ): Delivery {

        return DB::transaction(function () use ($delivery) {

            $delivery = Delivery::query()
                ->with('driver')
                ->lockForUpdate()
                ->findOrFail($delivery->id);

            $updated = $this->deliveryService->updateStatus(
                $delivery,
                DeliveryStatusEnum::DELIVERED
            );

            /*
            |--------------------------------------------------------------------------
            | Libérer le livreur
            |--------------------------------------------------------------------------
            */

            if ($updated->driver) {
                $updated->driver->status =
                    DriverStatusEnum::ONLINE;

                $updated->driver->save();
            }

            return $updated->fresh([
                'driver',
                'order',
                'histories',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Échec de livraison
    |--------------------------------------------------------------------------
    */

    public function fail(
        Delivery $delivery,
        string $reason
    ): Delivery {

        return DB::transaction(function () use (
            $delivery,
            $reason
        ) {

            $delivery = Delivery::query()
                ->with('driver')
                ->lockForUpdate()
                ->findOrFail($delivery->id);

            $updated = $this->deliveryService->updateStatus(
                $delivery,
                DeliveryStatusEnum::FAILED
            );

            $updated->failure_reason = $reason;
            $updated->save();

            /*
            |--------------------------------------------------------------------------
            | Libérer le livreur
            |--------------------------------------------------------------------------
            */

            if ($updated->driver) {
                $updated->driver->status =
                    DriverStatusEnum::ONLINE;

                $updated->driver->save();
            }

            return $updated->fresh([
                'driver',
                'order',
                'histories',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Annuler une livraison
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Delivery $delivery,
        string $reason
    ): Delivery {

        return DB::transaction(function () use (
            $delivery,
            $reason
        ) {

            $delivery = Delivery::query()
                ->with('driver')
                ->lockForUpdate()
                ->findOrFail($delivery->id);

            $updated = $this->deliveryService->updateStatus(
                $delivery,
                DeliveryStatusEnum::CANCELLED
            );

            $updated->cancel_reason = $reason;
            $updated->save();

            /*
            |--------------------------------------------------------------------------
            | Libérer le livreur
            |--------------------------------------------------------------------------
            */

            if ($updated->driver) {
                $updated->driver->status =
                    DriverStatusEnum::ONLINE;

                $updated->driver->save();
            }

            return $updated->fresh([
                'driver',
                'order',
                'histories',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Transition générique
    |--------------------------------------------------------------------------
    */

    protected function transition(
        Delivery $delivery,
        DeliveryStatusEnum $status
    ): Delivery {

        return DB::transaction(function () use (
            $delivery,
            $status
        ) {

            $delivery = Delivery::query()
                ->lockForUpdate()
                ->findOrFail($delivery->id);

            if (!$delivery->driver_id) {
                throw new RuntimeException(
                    'Cette livraison n’a aucun livreur assigné.'
                );
            }

            return $this->deliveryService->updateStatus(
                $delivery,
                $status
            );
        });
    }
}
