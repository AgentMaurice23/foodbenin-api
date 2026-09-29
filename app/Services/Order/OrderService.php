<?php

namespace App\Services\Order;

use App\Enums\OrderStatusEnum;
use App\Models\Order;

use Exception;

class OrderService
{
    /*
    |--------------------------------------------------------------------------
    | validateTransition()
    |--------------------------------------------------------------------------
    |
    | Vérifie qu'une transition d'état est autorisée.
    |
    | Exemple :
    |
    | pending     -> confirmed
    | confirmed   -> preparing
    | preparing   -> ready
    |
    */

    public function validateTransition(
        Order $order,
        OrderStatusEnum $newStatus
    ): bool {

        $allowedTransitions = [

            OrderStatusEnum::PENDING->value => [

                OrderStatusEnum::CONFIRMED,

                OrderStatusEnum::CANCELLED,
            ],

            OrderStatusEnum::CONFIRMED->value => [

                OrderStatusEnum::PREPARING,

                OrderStatusEnum::CANCELLED,
            ],

            OrderStatusEnum::PREPARING->value => [

                OrderStatusEnum::READY,

                OrderStatusEnum::CANCELLED,
            ],

            OrderStatusEnum::READY->value => [

                OrderStatusEnum::PICKED_UP,
            ],

            OrderStatusEnum::PICKED_UP->value => [

                OrderStatusEnum::DELIVERING,
            ],

            OrderStatusEnum::DELIVERING->value => [

                OrderStatusEnum::DELIVERED,
            ],
        ];

        return in_array(

            $newStatus,

            $allowedTransitions[
                $order
                    ->status
                    ->value
            ] ?? []
        );
    }

    /*
    |--------------------------------------------------------------------------
    | updateStatus()
    |--------------------------------------------------------------------------
    |
    | Effectue un changement d'état sécurisé.
    |
    | Garanties :
    |
    | ✓ validation workflow
    | ✓ mise à jour status
    | ✓ mise à jour timestamps
    | ✓ retour objet frais
    |
    */

    public function updateStatus(
        Order $order,
        OrderStatusEnum $status,
        string $role
    ): Order {

        if (

            !$this->validateOwnership(

                $order,

                auth()->user()

            )

        ) {

            throw new Exception(

                'Commande non autorisée.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validation permission
        |--------------------------------------------------------------------------
        */

        if (

            !$this->validateRoleTransition(

                $role,

                $status
            )

        ) {

            throw new Exception(

                'Action non autorisée.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validation transition
        |--------------------------------------------------------------------------
        */

        if (

            !$this->validateTransition(
                $order,
                $newStatus
            )

        ) {

            throw new Exception(
                'Transition invalide.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Mise à jour statut
        |--------------------------------------------------------------------------
        */

        $order->update([

            'status' => $newStatus,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Mise à jour des timestamps métier
        |--------------------------------------------------------------------------
        */

        switch ($newStatus) {

            case OrderStatusEnum::CONFIRMED:

                $order->update([

                    'confirmed_at' => now(),
                ]);

                break;

            case OrderStatusEnum::PREPARING:

                $order->update([

                    'preparing_at' => now(),
                ]);

                break;

            case OrderStatusEnum::READY:

                $order->update([

                    'ready_at' => now(),
                ]);

                break;

            case OrderStatusEnum::PICKED_UP:

                $order->update([

                    'picked_up_at' => now(),
                ]);

                break;

            case OrderStatusEnum::DELIVERING:

                $order->update([

                    'delivering_at' => now(),
                ]);

                break;

            case OrderStatusEnum::DELIVERED:

                $order->update([

                    'delivered_at' => now(),
                ]);

                break;

            case OrderStatusEnum::CANCELLED:

                $order->update([

                    'cancelled_at' => now(),
                ]);

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Retour
        |--------------------------------------------------------------------------
        */

        return $order->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | cancel()
    |--------------------------------------------------------------------------
    |
    | Annule une commande.
    |
    */

    public function cancel(
        Order $order
    ): Order {

        return $this->updateStatus(

            $order,

            OrderStatusEnum::CANCELLED
        );
    }

    /*
    |--------------------------------------------------------------------------
    | confirm()
    |--------------------------------------------------------------------------
    */

    public function confirm(
        Order $order
    ): Order {

        return $this->updateStatus(

            $order,

            OrderStatusEnum::CONFIRMED
        );
    }

    /*
    |--------------------------------------------------------------------------
    | preparing()
    |--------------------------------------------------------------------------
    */

    public function preparing(
        Order $order
    ): Order {

        return $this->updateStatus(

            $order,

            OrderStatusEnum::PREPARING
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ready()
    |--------------------------------------------------------------------------
    */

    public function ready(
        Order $order
    ): Order {

        return $this->updateStatus(

            $order,

            OrderStatusEnum::READY
        );
    }

    /*
    |--------------------------------------------------------------------------
    | pickedUp()
    |--------------------------------------------------------------------------
    */

    public function pickedUp(
        Order $order
    ): Order {

        return $this->updateStatus(

            $order,

            OrderStatusEnum::PICKED_UP
        );
    }

    /*
    |--------------------------------------------------------------------------
    | delivering()
    |--------------------------------------------------------------------------
    */

    public function delivering(
        Order $order
    ): Order {

        return $this->updateStatus(

            $order,

            OrderStatusEnum::DELIVERING
        );
    }

    /*
    |--------------------------------------------------------------------------
    | delivered()
    |--------------------------------------------------------------------------
    */

    public function delivered(
        Order $order
    ): Order {

        return $this->updateStatus(

            $order,

            OrderStatusEnum::DELIVERED
        );
    }

    /*
    |--------------------------------------------------------------------------
    | getAllowedTransitions()
    |--------------------------------------------------------------------------
    |
    | Retourne les transitions autorisées
    | pour un acteur donné.
    |
    */

    public function getAllowedTransitions(
        string $role
    ): array {

        return [

            'super_admin' => [

                OrderStatusEnum::CONFIRMED,

                OrderStatusEnum::PREPARING,

                OrderStatusEnum::READY,

                OrderStatusEnum::PICKED_UP,

                OrderStatusEnum::DELIVERING,

                OrderStatusEnum::DELIVERED,

                OrderStatusEnum::CANCELLED,
            ],

            'restaurant_owner' => [

                OrderStatusEnum::CONFIRMED,

                OrderStatusEnum::PREPARING,

                OrderStatusEnum::READY,

                OrderStatusEnum::CANCELLED,
            ],

            'delivery_man' => [

                OrderStatusEnum::PICKED_UP,

                OrderStatusEnum::DELIVERING,

                OrderStatusEnum::DELIVERED,
            ],

            'customer' => [

                OrderStatusEnum::CANCELLED,
            ],
        ][$role] ?? [];
    }

    /*
    |--------------------------------------------------------------------------
    | Validation propriétaire
    |--------------------------------------------------------------------------
    */

    public function validateOwnership(
        Order $order,
        $user
    ): bool {

        if (

            $user->hasRole(
                'super_admin'
            )

        ) {

            return true;
        }

        if (

            $user->hasRole(
                'restaurant_owner'
            )

        ) {

            return
                $order
                    ->restaurant
                    ->owner_id
                ==
                $user->id;
        }

        if (

            $user->hasRole(
                'customer'
            )

        ) {

            return
                $order
                    ->user_id
                ==
                $user->id;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | validateRoleTransition()
    |--------------------------------------------------------------------------
    |
    | Vérifie qu'un acteur a le droit
    | d'effectuer cette transition.
    |
    */

    public function validateRoleTransition(
        string $role,
        OrderStatusEnum $status
    ): bool {

        return in_array(

            $status,

            $this->getAllowedTransitions(
                $role
            )
        );
    }
}