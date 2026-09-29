<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table delivery_proofs.
     *
     * Cette table conserve les différentes preuves
     * pouvant être associées à une livraison.
     */
    public function up(): void
    {
        Schema::create('delivery_proofs', function (Blueprint $table) {

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
            */

            $table->uuid('uuid')
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Livraison
            |--------------------------------------------------------------------------
            |
            | Une preuve appartient à une livraison.
            |
            */

            $table->foreignId('delivery_id')
                ->constrained('deliveries')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Type de preuve
            |--------------------------------------------------------------------------
            */

            $table->enum('type', [

                'photo',

                'signature',

                'otp',

                'customer_confirmation',

            ]);

            /*
            |--------------------------------------------------------------------------
            | Valeur de la preuve
            |--------------------------------------------------------------------------
            |
            | Exemple :
            |
            | photo               → chemin du fichier
            | signature            → chemin du fichier
            | otp                 → valeur sécurisée / référence
            | customer_confirmation → référence de confirmation
            |
            */

            $table->text('value')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Informations supplémentaires
            |--------------------------------------------------------------------------
            */

            $table->json('metadata')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Utilisateur ayant enregistré la preuve
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Date de création
            |--------------------------------------------------------------------------
            |
            | Une preuve est un élément historique.
            | Nous n'avons donc pas besoin de updated_at.
            |
            */

            $table->timestamp('created_at')
                ->useCurrent();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index([
                'delivery_id',
                'type'
            ]);
        });
    }

    /**
     * Supprime la table delivery_proofs.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_proofs');
    }
};