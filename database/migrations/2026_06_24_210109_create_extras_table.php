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
        Schema::create('extras', function (Blueprint $table) {

            $table->id();

            $table->foreignId('restaurant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->uuid('uuid')
                ->unique();

            $table->string('name');

            $table->decimal(
                'price',
                10,
                2
            );

            $table->boolean('is_available')
                ->default(true);

            $table->integer('position')
                ->default(0);

            $table->timestamps();

            $table->softDeletes();

            $table->index('restaurant_id');

            $table->index('is_available');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extras');
    }
};
