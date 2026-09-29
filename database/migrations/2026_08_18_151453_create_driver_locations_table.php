<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table driver_locations.
     *
     * Cette table conserve l'historique des positions GPS
     * enregistrées pour un livreur.
     */
    public function up(): void
    {
        Schema::create('driver_locations', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Identifiant
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Livreur
            |--------------------------------------------------------------------------
            |
            | Chaque position appartient à un Driver.
            |
            */

            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Livraison
            |--------------------------------------------------------------------------
            |
            | La position peut être associée à une livraison.
            |
            | Elle reste nullable car un livreur peut également
            | envoyer sa position lorsqu'il est simplement ONLINE.
            |
            */

            $table->foreignId('delivery_id')
                ->nullable()
                ->constrained('deliveries')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Position GPS
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'latitude',
                10,
                7
            );

            $table->decimal(
                'longitude',
                10,
                7
            );

            /*
            |--------------------------------------------------------------------------
            | Précision GPS
            |--------------------------------------------------------------------------
            |
            | Valeur en mètres fournie par le GPS du téléphone.
            |
            */

            $table->decimal(
                'accuracy',
                8,
                2
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | Vitesse
            |--------------------------------------------------------------------------
            |
            | Vitesse du livreur en km/h.
            |
            */

            $table->decimal(
                'speed',
                8,
                2
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | Direction
            |--------------------------------------------------------------------------
            |
            | Direction du déplacement en degrés.
            |
            */

            $table->decimal(
                'heading',
                6,
                2
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | Date de collecte
            |--------------------------------------------------------------------------
            |
            | Timestamp provenant du GPS/appareil.
            |
            */

            $table->timestamp('recorded_at');

            /*
            |--------------------------------------------------------------------------
            | Timestamp Laravel
            |--------------------------------------------------------------------------
            */

            $table->timestamp('created_at')
                ->useCurrent();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            |
            | Ces index sont importants pour les requêtes fréquentes :
            |
            | - dernière position d'un livreur
            | - positions d'une livraison
            | - historique GPS
            |
            */

            $table->index([
                'driver_id',
                'recorded_at'
            ]);

            $table->index([
                'delivery_id',
                'recorded_at'
            ]);
        });
    }

    /**
     * Supprime la table driver_locations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_locations');
    }
};