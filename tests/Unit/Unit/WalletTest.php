<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_a_wallet(): void
    {
        $user = User::factory()->create();

        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(
            $wallet->user->is($user)
        );

        $this->assertTrue(
            $user->wallet->is($wallet)
        );
    }

    public function test_new_wallet_has_zero_balance(): void
    {
        $user = User::factory()->create();

        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(
            0,
            (float) $wallet->balance
        );
    }

    public function test_wallet_balance_can_be_decimal(): void
    {
        $user = User::factory()->create();

        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 12500.50,
        ]);

        $this->assertEquals(
            12500.50,
            (float) $wallet->fresh()->balance
        );
    }

    public function test_user_cannot_have_two_wallets(): void
    {
        $user = User::factory()->create();

        Wallet::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->expectException(\Throwable::class);

        Wallet::factory()->create([
            'user_id' => $user->id,
        ]);
    }
}