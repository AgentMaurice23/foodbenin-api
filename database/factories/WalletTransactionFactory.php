<?php

namespace Database\Factories;

use App\Enums\WalletTransactionStatusEnum;
use App\Enums\WalletTransactionTypeEnum;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WalletTransactionFactory extends Factory
{
    protected $model = WalletTransaction::class;

    public function definition(): array
    {
        return [
            'wallet_id' => Wallet::factory(),

            'type' => WalletTransactionTypeEnum::CREDIT,

            'amount' => 10000.00,

            'status' => WalletTransactionStatusEnum::COMPLETED,

            'reference' => 'WLT-' . strtoupper(Str::random(12)),

            'description' => 'Transaction wallet',

            'metadata' => [],

            'completed_at' => now(),
        ];
    }
}