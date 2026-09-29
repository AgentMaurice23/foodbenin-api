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
        Schema::create('restaurants', function (Blueprint $table) {

            $table->id();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('city_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('zone_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('slug')->unique();

            $table->text('description')->nullable();

            $table->string('phone');

            $table->string('email')->nullable();

            $table->string('logo')->nullable();

            $table->string('cover')->nullable();

            $table->string('address')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();

            $table->decimal('longitude', 10, 7)->nullable();

            $table->decimal('rating', 3, 2)->default(0);

            $table->integer('reviews_count')->default(0);

            $table->decimal('delivery_fee', 10, 2)->default(0);

            $table->decimal('minimum_order', 10, 2)->default(0);

            $table->uuid('uuid')->unique();

            $table->softDeletes();

            $table->boolean('is_open')->default(true);

            $table->boolean('is_verified')->default(false);

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'suspended',
                'closed'
            ])->default('pending');
            $table->index('status');
            
            $table->text('rejection_reason')->nullable();
            $table->text('suspension_reason')->nullable();

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamp('suspended_at')
                ->nullable();

            $table->timestamp('verified_at')
                ->nullable();
                
            $table->timestamp('closed_at')
                ->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
