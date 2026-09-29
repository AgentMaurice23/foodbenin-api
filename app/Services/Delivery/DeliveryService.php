<?php

namespace App\Services\Delivery;


use App\Models\Driver;
use App\Enums\DeliveryStatusEnum;
use App\Enums\DriverStatusEnum;
use App\Models\Delivery;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DeliveryService
{

    /*
    |--------------------------------------------------------------------------
    | Assigner un livreur
    |--------------------------------------------------------------------------
    |
    | Cette méthode associe un Driver disponible à une Delivery.
    |
    | Règles métier :
    |
    | 1. La livraison doit être en attente.
    | 2. Le livreur doit être ONLINE.
    | 3. Le livreur ne doit pas être suspendu ou déjà occupé.
    | 4. Le Driver devient BUSY.
    | 5. La Delivery devient ASSIGNED.
    | 6. assigned_at est enregistré.
    |
    | Toutes les opérations sont exécutées dans une transaction
    | afin d'éviter un état incohérent entre Driver et Delivery.
    |
    */

    public function assignDriver(
        Delivery $delivery,
        Driver $driver
    ): Delivery {

        return DB::transaction(function () use (
            $delivery,
            $driver
        ) {

            /*
            |--------------------------------------------------------------------------
            | Recharger et verrouiller la livraison
            |--------------------------------------------------------------------------
            |
            | lockForUpdate() empêche une autre transaction de modifier
            | simultanément cette livraison pendant notre opération.
            |
            */

            $delivery = Delivery::query()
                ->lockForUpdate()
                ->findOrFail($delivery->id);

            /*
            |--------------------------------------------------------------------------
            | Recharger et verrouiller le livreur
            |--------------------------------------------------------------------------
            |
            | C'est particulièrement important ici.
            |
            | Sans verrouillage, deux requêtes simultanées pourraient
            | sélectionner le même livreur ONLINE.
            |
            */

            $driver = Driver::query()
                ->lockForUpdate()
                ->findOrFail($driver->id);

            /*
            |--------------------------------------------------------------------------
            | Vérifier l'état de la livraison
            |--------------------------------------------------------------------------
            |
            | Une livraison déjà assignée ne doit pas pouvoir être
            | réassignée accidentellement par cette méthode.
            |
            */

            if (
                $delivery->status !==
                DeliveryStatusEnum::PENDING
            ) {

                throw new RuntimeException(
                    'Cette livraison ne peut pas être assignée.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Vérifier le statut du livreur
            |--------------------------------------------------------------------------
            |
            | Seul un livreur ONLINE est disponible pour recevoir
            | une nouvelle livraison.
            |
            */

            if (
                $driver->status !==
                DriverStatusEnum::ONLINE
            ) {

                throw new RuntimeException(
                    'Ce livreur n’est pas disponible.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Vérifier la vérification du livreur
            |--------------------------------------------------------------------------
            |
            | Un livreur non vérifié ne doit pas recevoir de mission
            | de livraison.
            |
            */

            if (!$driver->is_verified) {

                throw new RuntimeException(
                    'Ce livreur n’est pas encore vérifié.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Assignation du livreur
            |--------------------------------------------------------------------------
            */

            $delivery->driver_id =
                $driver->id;

            /*
            |--------------------------------------------------------------------------
            | Changement du statut de la livraison
            |--------------------------------------------------------------------------
            */

            $delivery->status =
                DeliveryStatusEnum::ASSIGNED;

            /*
            |--------------------------------------------------------------------------
            | Date d'assignation
            |--------------------------------------------------------------------------
            */

            $delivery->assigned_at = now();

            /*
            |--------------------------------------------------------------------------
            | Sauvegarder la livraison
            |--------------------------------------------------------------------------
            */

            $delivery->save();

            /*
            |--------------------------------------------------------------------------
            | Le livreur devient occupé
            |--------------------------------------------------------------------------
            |
            | Dès qu'une mission lui est attribuée, il ne doit plus
            | être considéré comme disponible pour une autre mission.
            |
            */

            $driver->status =
                DriverStatusEnum::BUSY;

            $driver->save();

            /*
            |--------------------------------------------------------------------------
            | Historique
            |--------------------------------------------------------------------------
            |
            | Si DeliveryHistory existe déjà dans ton architecture,
            | on enregistre le changement de statut.
            |
            */

            $delivery->histories()->create([
                'status' =>
                    DeliveryStatusEnum::ASSIGNED->value,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Retourner la livraison
            |--------------------------------------------------------------------------
            */

            return $delivery->fresh([
                'driver',
                'histories',
            ]);
        });
    }
    /*
    |--------------------------------------------------------------------------
    | Change le statut d'une livraison.
    |--------------------------------------------------------------------------
    |
    | Cette méthode :
    |
    | 1. vérifie que la transition est autorisée ;
    | 2. met à jour le statut ;
    | 3. enregistre la date correspondante ;
    | 4. retourne la livraison mise à jour.
    |
    */

    public function updateStatus(
        Delivery $delivery,
        DeliveryStatusEnum $newStatus
    ): Delivery {

        return DB::transaction(
            function () use (
                $delivery,
                $newStatus
            ) {

                /*
                |--------------------------------------------------------------------------
                | Statut actuel
                |--------------------------------------------------------------------------
                */

                $currentStatus =
                    $delivery->status;

                /*
                |--------------------------------------------------------------------------
                | Vérification de la transition
                |--------------------------------------------------------------------------
                */

                if (
                    !$currentStatus->canTransitionTo(
                        $newStatus
                    )
                ) {

                    throw new RuntimeException(
                        sprintf(
                            'Transition impossible : %s → %s.',
                            $currentStatus->value,
                            $newStatus->value
                        )
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Mise à jour du statut
                |--------------------------------------------------------------------------
                */

                $delivery->status =
                    $newStatus;

                /*
                |--------------------------------------------------------------------------
                | Mise à jour automatique de l'horodatage
                |--------------------------------------------------------------------------
                */

                $this->updateTimestamp(
                    $delivery,
                    $newStatus
                );

                /*
                |--------------------------------------------------------------------------
                | Sauvegarde
                |--------------------------------------------------------------------------
                */

                $delivery->save();

                /*
                |--------------------------------------------------------------------------
                | Retour
                |--------------------------------------------------------------------------
                */

                return $delivery->fresh();
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Met à jour l'horodatage correspondant au statut.
    |--------------------------------------------------------------------------
    */

    protected function updateTimestamp(
        Delivery $delivery,
        DeliveryStatusEnum $status
    ): void {

        $now = now();

        match ($status) {

            DeliveryStatusEnum::ASSIGNED =>
                $delivery->assigned_at = $now,

            DeliveryStatusEnum::ACCEPTED =>
                $delivery->accepted_at = $now,

            DeliveryStatusEnum::GOING_TO_RESTAURANT =>
                $delivery->going_to_restaurant_at = $now,

            DeliveryStatusEnum::AT_RESTAURANT =>
                $delivery->arrived_restaurant_at = $now,

            DeliveryStatusEnum::PICKED_UP =>
                $delivery->picked_up_at = $now,

            DeliveryStatusEnum::ON_THE_WAY =>
                $delivery->on_the_way_at = $now,

            DeliveryStatusEnum::ARRIVED =>
                $delivery->arrived_at = $now,

            DeliveryStatusEnum::DELIVERED =>
                $delivery->delivered_at = $now,

            DeliveryStatusEnum::FAILED =>
                $delivery->failed_at = $now,

            DeliveryStatusEnum::CANCELLED =>
                $delivery->cancelled_at = $now,

            DeliveryStatusEnum::PENDING => null,
        };
    }
}