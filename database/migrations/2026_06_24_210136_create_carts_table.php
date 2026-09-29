<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('restaurant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->uuid('uuid')
                ->unique();

            $table->decimal(
                'subtotal',
                10,
                2
            )->default(0);

            $table->decimal(
                'delivery_fee',
                10,
                2
            )->default(0);

            $table->decimal(
                'discount',
                10,
                2
            )->default(0);

            $table->decimal(
                'total',
                10,
                2
            )->default(0);

            $table->timestamps();

            $table->softDeletes();

            $table->unique([
                'user_id',
                'restaurant_id'
            ]);

            $table->index('user_id');

            $table->index('restaurant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
