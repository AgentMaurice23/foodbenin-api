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
        Schema::create('plans', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            $table->decimal('price', 10, 2);

            $table->decimal('commission_rate', 5, 2);

            $table->integer('max_products')->nullable();

            $table->integer('max_employees')->nullable();

            $table->boolean('featured_restaurant')
                ->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
