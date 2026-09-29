<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Crée la table des livreurs.
     *
     * Un Driver est un profil métier rattaché à un User.
     * Le User conserve les informations générales du compte,
     * tandis que Driver contient toutes les informations
     * spécifiques à l'activité de livraison.
     */
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Identifiant
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Identifiant public
            |--------------------------------------------------------------------------
            |
            | Utilisé pour exposer le livreur dans l'API sans utiliser
            | directement son identifiant numérique.
            |
            */

            $table->uuid('uuid')->unique();

            /*
            |--------------------------------------------------------------------------
            | Relation avec User
            |--------------------------------------------------------------------------
            |
            | Un utilisateur possède au maximum un profil Driver.
            |
            */

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Statut du livreur
            |--------------------------------------------------------------------------
            |
            | offline   : ne reçoit aucune livraison.
            | online    : disponible pour recevoir une livraison.
            | busy      : possède actuellement une livraison active.
            | suspended : interdit temporairement d'utiliser le service.
            |
            */

            $table->enum('status', [

                'offline',

                'online',

                'busy',

                'suspended',

            ])->default('offline');

            /*
            |--------------------------------------------------------------------------
            | Type de véhicule
            |--------------------------------------------------------------------------
            |
            | Préparé pour plusieurs moyens de déplacement.
            |
            */

            $table->enum('vehicle_type', [

                'motorbike',

                'bicycle',

                'car',

                'tricycle',

                'walking',

            ]);

            /*
            |--------------------------------------------------------------------------
            | Informations du véhicule
            |--------------------------------------------------------------------------
            */

            $table->string('vehicle_brand')
                ->nullable();

            $table->string('vehicle_model')
                ->nullable();

            $table->string('plate_number')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Documents du livreur
            |--------------------------------------------------------------------------
            |
            | Ces champs seront utilisés plus tard pour le processus
            | de vérification/KYC du livreur.
            |
            */

            $table->string('driver_license')
                ->nullable();

            $table->string('identity_document')
                ->nullable();

            $table->string('insurance_document')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Vérification
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_verified')
                ->default(false);

            $table->timestamp('verified_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Statistiques
            |--------------------------------------------------------------------------
            |
            | Ces valeurs pourront être mises à jour par le système
            | après chaque livraison terminée.
            |
            */

            $table->unsignedInteger('completed_deliveries')
                ->default(0);

            $table->decimal('rating', 3, 2)
                ->default(5.00);

            $table->unsignedInteger('ratings_count')
                ->default(0);

            $table->decimal('total_distance', 12, 2)
                ->default(0);

            $table->decimal('total_earnings', 12, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Dernière position connue
            |--------------------------------------------------------------------------
            |
            | Nous conserverons ici uniquement la dernière position connue
            | du livreur.
            |
            | L'historique complet des positions sera stocké dans
            | driver_locations.
            |
            */

            $table->decimal('last_latitude', 10, 7)
                ->nullable();

            $table->decimal('last_longitude', 10, 7)
                ->nullable();

            $table->timestamp('last_location_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Soft Delete
            |--------------------------------------------------------------------------
            |
            | Nous ne supprimons pas définitivement le profil Driver.
            | Cela permet de conserver l'historique métier.
            |
            */

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Supprime la table drivers.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};