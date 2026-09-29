<?php

namespace App\Enums;

enum DeliveryStatusEnum: string
{
    /*
    |--------------------------------------------------------------------------
    | Statuts
    |--------------------------------------------------------------------------
    */

    case PENDING = 'pending';

    case ASSIGNED = 'assigned';

    case ACCEPTED = 'accepted';

    case GOING_TO_RESTAURANT = 'going_to_restaurant';

    case AT_RESTAURANT = 'at_restaurant';

    case PICKED_UP = 'picked_up';

    case ON_THE_WAY = 'on_the_way';

    case ARRIVED = 'arrived';

    case DELIVERED = 'delivered';

    case FAILED = 'failed';

    case CANCELLED = 'cancelled';


    /*
    |--------------------------------------------------------------------------
    | Vérifie si la livraison peut passer au statut suivant.
    |--------------------------------------------------------------------------
    */

    public function canTransitionTo(
    self $newStatus
    ): bool {

    
        return match ($this) {

            self::PENDING =>
                $newStatus === self::ASSIGNED,

            self::ASSIGNED =>
                in_array($newStatus, [
                    self::ACCEPTED,
                    self::CANCELLED,
                ], true),

            self::ACCEPTED =>
                in_array($newStatus, [
                    self::GOING_TO_RESTAURANT,
                    self::CANCELLED,
                ], true),

            self::GOING_TO_RESTAURANT =>
                in_array($newStatus, [
                    self::AT_RESTAURANT,
                    self::FAILED,
                    self::CANCELLED,
                ], true),

            self::AT_RESTAURANT =>
                in_array($newStatus, [
                    self::PICKED_UP,
                    self::FAILED,
                    self::CANCELLED,
                ], true),

            self::PICKED_UP =>
                in_array($newStatus, [
                    self::ON_THE_WAY,
                    self::FAILED,
                    self::CANCELLED,
                ], true),

            self::ON_THE_WAY =>
                in_array($newStatus, [
                    self::ARRIVED,
                    self::FAILED,
                    self::CANCELLED,
                ], true),

            self::ARRIVED =>
                in_array($newStatus, [
                    self::DELIVERED,
                    self::FAILED,
                    self::CANCELLED,
                ], true),

            self::DELIVERED,
            self::FAILED,
            self::CANCELLED =>
                false,
        };
    

    }



    /*
    |--------------------------------------------------------------------------
    | Vérifie si le statut est terminal.
    |--------------------------------------------------------------------------
    */

    public function isFinal(): bool
    {
        return match ($this) {

            self::DELIVERED,
            self::FAILED,
            self::CANCELLED => true,

            default => false,
        };
    }
}