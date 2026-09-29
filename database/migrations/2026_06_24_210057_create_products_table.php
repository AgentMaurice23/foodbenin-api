<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            $table->foreignId('restaurant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->uuid('uuid')
                ->unique();

            $table->string('name');

            $table->string('slug');

            $table->text('description')
                ->nullable();

            // Prix
            $table->decimal('price', 10, 2);

            $table->decimal('sale_price', 10, 2)
                ->nullable();

            // Compatibilité ancienne image
            $table->string('image')
                ->nullable();

            // Disponibilité
            $table->boolean('is_available')
                ->default(true);

            $table->boolean('is_featured')
                ->default(false);

            // Variantes et extras
            $table->boolean('has_variants')
                ->default(false);

            $table->boolean('has_extras')
                ->default(false);

            // Gestion des stocks
            $table->enum('stock_type', [
                'unlimited',
                'limited',
                'out_of_stock'
            ])->default('unlimited');

            $table->integer('stock_quantity')
                ->nullable();

            // Statistiques
            $table->unsignedInteger('sold_count')
                ->default(0);

            // Temps préparation
            $table->integer('preparation_time')
                ->default(15);

            $table->timestamps();

            $table->softDeletes();

            $table->unique([
                'restaurant_id',
                'slug'
            ]);

            $table->index('restaurant_id');

            $table->index('category_id');

            $table->index('is_available');

            $table->index('is_featured');

            $table->index('stock_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
