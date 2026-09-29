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
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            $table->string('order_number')
                ->unique();

            $table->foreignId('restaurant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('address_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('subtotal', 10, 2);

            $table->decimal('delivery_fee', 10, 2)
                ->default(0);

            $table->decimal('discount', 10, 2)
                ->default(0);

            $table->decimal('total', 10, 2);

            $table->enum('status', [

                'pending',

                'accepted',

                'preparing',

                'ready_for_packaging',

                'ready_for_delivery',

                'on_the_way',

                'delivered',

                'cancelled'

            ])->default('pending');

            $table->text('notes')
                ->nullable();
                
            $table->uuid('uuid')->unique();

            $table->softDeletes();
                
            $table->boolean('is_paid')
                ->default(false);
            
            $table->timestamp('estimated_delivery_at')
                ->nullable();

            $table->timestamp('delivered_at')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
