<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wallet_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'credit',
                'debit',
            ]);

            $table->decimal('amount', 15, 2);

            $table->enum('status', [
                'pending',
                'completed',
                'failed',
                'cancelled',
            ])->default('pending');

            $table->string('reference')->unique();

            $table->string('description')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};