<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table delivery_histories.
     *
     * Cette table conserve chaque changement d'état
     * effectué pendant le cycle de vie d'une livraison.
     */
    public function up(): void
    {
        Schema::create('delivery_histories', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Identifiant
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Livraison concernée
            |--------------------------------------------------------------------------
            |
            | Si une livraison est définitivement supprimée,
            | son historique est également supprimé.
            |
            */

            $table->foreignId('delivery_id')
                ->constrained('deliveries')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Statut
            |--------------------------------------------------------------------------
            |
            | Nous conservons le statut au moment où l'événement
            | s'est produit.
            |
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

            ]);

            /*
            |--------------------------------------------------------------------------
            | Auteur du changement
            |--------------------------------------------------------------------------
            |
            | Peut être un utilisateur connecté :
            |
            | - livreur
            | - restaurant
            | - administrateur
            |
            | NULL permet également aux traitements automatiques
            | du système de créer une entrée.
            |
            */

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Commentaire
            |--------------------------------------------------------------------------
            */

            $table->text('comment')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Données supplémentaires
            |--------------------------------------------------------------------------
            |
            | JSON permettant d'ajouter ultérieurement des informations
            | sans modifier immédiatement la structure de la table.
            |
            | Exemple :
            |
            | {
            |     "reason": "client_absent",
            |     "attempt": 1
            | }
            |
            */

            $table->json('metadata')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Date de création
            |--------------------------------------------------------------------------
            |
            | Nous n'avons pas besoin de updated_at :
            | un historique ne doit normalement jamais être modifié.
            |
            */

            $table->timestamp('created_at')
                ->useCurrent();
        });
    }

    /**
     * Supprime la table delivery_histories.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_histories');
    }
};