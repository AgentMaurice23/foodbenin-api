<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_can_have_transactions(): void
    {
        $wallet = Wallet::factory()->create();

        $transaction = WalletTransaction::factory()->create([
            'wallet_id' => $wallet->id,
        ]);

        $this->assertTrue(
            $transaction->wallet->is($wallet)
        );
    }

    public function test_transaction_belongs_to_user_through_wallet(): void
    {
        $user = User::factory()->create();

        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
        ]);

        $transaction = WalletTransaction::factory()->create([
            'wallet_id' => $wallet->id,
        ]);

        $this->assertTrue(
            $transaction->wallet->user->is($user)
        );
    }

    public function test_transaction_amount_is_decimal(): void
    {
        $transaction = WalletTransaction::factory()->create([
            'amount' => 15000.50,
        ]);

        $this->assertEquals(
            15000.50,
            (float) $transaction->fresh()->amount
        );
    }

    public function test_transaction_type_is_cast_to_enum(): void
    {
        $transaction = WalletTransaction::factory()->create();

        $this->assertInstanceOf(
            \App\Enums\WalletTransactionTypeEnum::class,
            $transaction->type
        );
    }

    public function test_transaction_status_is_cast_to_enum(): void
    {
        $transaction = WalletTransaction::factory()->create();

        $this->assertInstanceOf(
            \App\Enums\WalletTransactionStatusEnum::class,
            $transaction->status
        );
    }
}