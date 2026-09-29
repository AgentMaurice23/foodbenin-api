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
        Schema::create('restaurant_payment_methods', function (Blueprint $table) {

            $table->id();

            $table->foreignId('restaurant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('payment_method', [

                'cash',

                'mtn_momo',

                'moov_money',

                'celtis_cash',

                'card'

            ]);

            $table->boolean('active')
                ->default(true);
                

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_payment_methods');
    }
};
