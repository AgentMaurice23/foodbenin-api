<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table deliveries.
     *
     * Une livraison représente la mission de livraison
     * associée à une commande.
     */
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Identifiant
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | UUID public
            |--------------------------------------------------------------------------
            |
            | Permet d'identifier une livraison sans exposer
            | directement son ID numérique.
            |
            */

            $table->uuid('uuid')
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Commande
            |--------------------------------------------------------------------------
            |
            | Une commande possède une seule livraison.
            |
            */

            $table->foreignId('order_id')
                ->unique()
                ->constrained('orders')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Livreur
            |--------------------------------------------------------------------------
            |
            | Le livreur peut être NULL tant qu'aucun livreur
            | n'a encore été assigné.
            |
            */

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('drivers')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Statut
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [

                'pending',

                'assigned',

                'accepted',

                'going_to_restaurant',

                'at_restaurant',

                'picked_up',

                'on_the_way',

                'arrived',

                'delivered',

                'failed',

                'cancelled',

            ])->default('pending');

            /*
            |--------------------------------------------------------------------------
            | Tarification
            |--------------------------------------------------------------------------
            |
            | Les montants sont figés au moment de la livraison.
            |
            */

            $table->decimal(
                'delivery_fee',
                10,
                2
            )->default(0);

            $table->decimal(
                'driver_earning',
                10,
                2
            )->default(0);

            $table->decimal(
                'platform_commission',
                10,
                2
            )->default(0);

            /*
            |--------------------------------------------------------------------------
            | Estimation
            |--------------------------------------------------------------------------
            |
            | Distance en kilomètres.
            |
            | Durée en minutes.
            |
            */

            $table->decimal(
                'estimated_distance',
                10,
                2
            )->nullable();

            $table->unsignedInteger(
                'estimated_duration'
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | Horodatage de l'assignation
            |--------------------------------------------------------------------------
            */

            $table->timestamp('assigned_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Horodatage de l'acceptation
            |--------------------------------------------------------------------------
            */

            $table->timestamp('accepted_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Déplacement vers le restaurant
            |--------------------------------------------------------------------------
            */

            $table->timestamp('going_to_restaurant_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Arrivée au restaurant
            |--------------------------------------------------------------------------
            */

            $table->timestamp('arrived_restaurant_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Récupération de la commande
            |--------------------------------------------------------------------------
            */

            $table->timestamp('picked_up_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Départ du restaurant
            |--------------------------------------------------------------------------
            */

            $table->timestamp('on_the_way_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Arrivée chez le client
            |--------------------------------------------------------------------------
            */

            $table->timestamp('arrived_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Livraison terminée
            |--------------------------------------------------------------------------
            */

            $table->timestamp('delivered_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Livraison échouée
            |--------------------------------------------------------------------------
            */

            $table->timestamp('failed_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Livraison annulée
            |--------------------------------------------------------------------------
            */

            $table->timestamp('cancelled_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Raisons
            |--------------------------------------------------------------------------
            */

            $table->text('cancel_reason')
                ->nullable();

            $table->text('failure_reason')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Timestamps Laravel
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Suppression logique
            |--------------------------------------------------------------------------
            */

            $table->softDeletes();
        });
    }

    /**
     * Supprime la table deliveries.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};