<?php

namespace App\Services\Delivery;

use App\Enums\DeliveryStatusEnum;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\DriverLocation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TrackingService
{
    /*
    |--------------------------------------------------------------------------
    | Enregistrer une position GPS
    |--------------------------------------------------------------------------
    |
    | Une position peut être :
    |
    | - une simple position du livreur lorsqu'il est ONLINE ;
    | - une position liée à une livraison en cours.
    |
    | Lorsqu'une Delivery est fournie, elle doit obligatoirement
    | appartenir au Driver.
    |
    */

    public function recordLocation(
        Driver $driver,
        float $latitude,
        float $longitude,
        ?float $accuracy = null,
        ?float $speed = null,
        ?float $heading = null,
        ?Delivery $delivery = null,
        ?\DateTimeInterface $recordedAt = null
    ): DriverLocation {

        return DB::transaction(function () use (
            $driver,
            $latitude,
            $longitude,
            $accuracy,
            $speed,
            $heading,
            $delivery,
            $recordedAt
        ) {

            /*
            |--------------------------------------------------------------------------
            | Vérification de la latitude
            |--------------------------------------------------------------------------
            */

            if ($latitude < -90 || $latitude > 90) {
                throw new RuntimeException(
                    'La latitude est invalide.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Vérification de la longitude
            |--------------------------------------------------------------------------
            */

            if ($longitude < -180 || $longitude > 180) {
                throw new RuntimeException(
                    'La longitude est invalide.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Vérification de la précision GPS
            |--------------------------------------------------------------------------
            */

            if ($accuracy !== null && $accuracy < 0) {
                throw new RuntimeException(
                    'La précision GPS est invalide.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Vérification de la vitesse
            |--------------------------------------------------------------------------
            */

            if ($speed !== null && $speed < 0) {
                throw new RuntimeException(
                    'La vitesse est invalide.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Vérification du heading
            |--------------------------------------------------------------------------
            */

            if (
                $heading !== null &&
                ($heading < 0 || $heading >= 360)
            ) {
                throw new RuntimeException(
                    'La direction GPS est invalide.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Vérification de la livraison
            |--------------------------------------------------------------------------
            |
            | Si une livraison est associée à cette position :
            |
            | 1. elle doit appartenir au Driver ;
            | 2. elle ne doit pas être terminée ;
            | 3. elle doit être dans une phase permettant le tracking.
            |
            */

            if ($delivery !== null) {

                if ($delivery->driver_id !== $driver->id) {
                    throw new RuntimeException(
                        'Ce livreur n’est pas assigné à cette livraison.'
                    );
                }

                if (
                    $delivery->status->isFinal()
                ) {
                    throw new RuntimeException(
                        'Le tracking de cette livraison est terminé.'
                    );
                }

                if (
                    $delivery->status ===
                    DeliveryStatusEnum::PENDING
                ) {
                    throw new RuntimeException(
                        'Cette livraison n’est pas encore assignée.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Création de la position
            |--------------------------------------------------------------------------
            */

            return DriverLocation::create([

                'driver_id' =>
                    $driver->id,

                'delivery_id' =>
                    $delivery?->id,

                'latitude' =>
                    $latitude,

                'longitude' =>
                    $longitude,

                'accuracy' =>
                    $accuracy,

                'speed' =>
                    $speed,

                'heading' =>
                    $heading,

                'recorded_at' =>
                    $recordedAt ?? now(),
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Dernière position du livreur
    |--------------------------------------------------------------------------
    |
    | Retourne la position GPS la plus récente du Driver.
    |
    */

    public function getLatestDriverLocation(
        Driver $driver
    ): ?DriverLocation {

        return $driver
            ->locations()
            ->latest('recorded_at')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Dernière position d'une livraison
    |--------------------------------------------------------------------------
    |
    | Retourne la dernière position GPS enregistrée
    | pour une Delivery.
    |
    */

    public function getLatestDeliveryLocation(
        Delivery $delivery
    ): ?DriverLocation {

        return $delivery
            ->locations()
            ->latest('recorded_at')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Historique GPS d'une livraison
    |--------------------------------------------------------------------------
    |
    | Retourne toutes les positions enregistrées pour
    | reconstruire le trajet du livreur.
    |
    */

    public function getDeliveryLocations(
        Delivery $delivery
    ): Collection {

        return $delivery
            ->locations()
            ->orderBy('recorded_at')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Historique GPS d'un livreur
    |--------------------------------------------------------------------------
    |
    | Permet de récupérer les dernières positions générales
    | d'un livreur.
    |
    */

    public function getDriverLocations(
        Driver $driver,
        int $limit = 100
    ): Collection {

        if ($limit < 1) {
            throw new RuntimeException(
                'La limite doit être supérieure à zéro.'
            );
        }

        return $driver
            ->locations()
            ->latest('recorded_at')
            ->limit($limit)
            ->get();
    }
}
