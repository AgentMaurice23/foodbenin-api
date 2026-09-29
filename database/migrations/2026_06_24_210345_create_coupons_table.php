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
        Schema::create('coupons', function (Blueprint $table) {

            $table->id();

            $table->foreignId('restaurant_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->uuid('uuid')
                ->unique();

            $table->string('code')
                ->unique();

            $table->enum('type', [
                'fixed',
                'percentage'
            ]);

            $table->decimal(
                'value',
                10,
                2
            );

            $table->decimal(
                'minimum_amount',
                10,
                2
            )->default(0);

            $table->integer(
                'usage_limit'
            )->nullable();

            $table->integer(
                'used_count'
            )->default(0);

            $table->timestamp(
                'starts_at'
            )->nullable();

            $table->timestamp(
                'expires_at'
            )->nullable();

            $table->boolean(
                'is_active'
            )->default(true);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
