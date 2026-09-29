<?php

namespace Tests\Unit;

use App\Enums\WalletTransactionStatusEnum;
use App\Enums\WalletTransactionTypeEnum;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\Wallet\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class WalletServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WalletService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(WalletService::class);
    }

    public function test_it_creates_wallet_for_user(): void
    {
        $user = User::factory()->create();

        $wallet = $this->service->getOrCreateWallet($user);

        $this->assertInstanceOf(Wallet::class, $wallet);
        $this->assertEquals($user->id, $wallet->user_id);
        $this->assertEquals('0.00', $wallet->balance);
    }

    public function test_it_returns_existing_wallet(): void
    {
        $user = User::factory()->create();

        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 15000,
        ]);

        $result = $this->service->getOrCreateWallet($user);

        $this->assertEquals($wallet->id, $result->id);
        $this->assertEquals('15000.00', $result->balance);
    }

    public function test_it_credits_wallet(): void
    {
        $user = User::factory()->create();

        $wallet = $this->service->credit(
            user: $user,
            amount: 10000,
            reference: 'CREDIT-001',
            description: 'Recharge du portefeuille',
        );

        $this->assertEquals('10000.00', $wallet->balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'amount' => '10000.00',
            'reference' => 'CREDIT-001',
            'type' => WalletTransactionTypeEnum::CREDIT->value,
            'status' => WalletTransactionStatusEnum::COMPLETED->value,
        ]);
    }

    public function test_it_debits_wallet(): void
    {
        $user = User::factory()->create();

        $this->service->credit(
            user: $user,
            amount: 10000,
            reference: 'CREDIT-002',
        );

        $wallet = $this->service->debit(
            user: $user,
            amount: 3000,
            reference: 'DEBIT-001',
            description: 'Paiement commande',
        );

        $this->assertEquals('7000.00', $wallet->balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'amount' => '3000.00',
            'reference' => 'DEBIT-001',
            'type' => WalletTransactionTypeEnum::DEBIT->value,
            'status' => WalletTransactionStatusEnum::COMPLETED->value,
        ]);
    }

    public function test_it_rejects_insufficient_balance(): void
    {
        $user = User::factory()->create();

        $this->service->credit(
            user: $user,
            amount: 5000,
            reference: 'CREDIT-003',
        );

        $this->expectException(InvalidArgumentException::class);

        $this->service->debit(
            user: $user,
            amount: 6000,
            reference: 'DEBIT-002',
        );
    }

    public function test_it_rejects_zero_or_negative_amount(): void
    {
        $user = User::factory()->create();

        $this->expectException(InvalidArgumentException::class);

        $this->service->credit(
            user: $user,
            amount: 0,
            reference: 'INVALID-001',
        );
    }

    public function test_credit_is_idempotent_by_reference(): void
    {
        $user = User::factory()->create();

        $first = $this->service->credit(
            user: $user,
            amount: 10000,
            reference: 'IDEMPOTENT-001',
        );

        $second = $this->service->credit(
            user: $user,
            amount: 10000,
            reference: 'IDEMPOTENT-001',
        );

        $this->assertEquals($first->id, $second->id);
        $this->assertEquals('10000.00', $second->balance);

        $this->assertEquals(
            1,
            WalletTransaction::where('reference', 'IDEMPOTENT-001')->count()
        );
    }

    public function test_debit_is_idempotent_by_reference(): void
    {
        $user = User::factory()->create();

        $this->service->credit(
            user: $user,
            amount: 10000,
            reference: 'CREDIT-004',
        );

        $first = $this->service->debit(
            user: $user,
            amount: 3000,
            reference: 'IDEMPOTENT-002',
        );

        $second = $this->service->debit(
            user: $user,
            amount: 3000,
            reference: 'IDEMPOTENT-002',
        );

        $this->assertEquals($first->id, $second->id);
        $this->assertEquals('7000.00', $second->balance);

        $this->assertEquals(
            1,
            WalletTransaction::where('reference', 'IDEMPOTENT-002')->count()
        );
    }

    public function test_wallet_transaction_belongs_to_wallet(): void
    {
        $user = User::factory()->create();

        $wallet = $this->service->credit(
            user: $user,
            amount: 5000,
            reference: 'RELATION-001',
        );

        $transaction = WalletTransaction::where(
            'reference',
            'RELATION-001'
        )->firstOrFail();

        $this->assertEquals(
            $wallet->id,
            $transaction->wallet_id
        );
    }

    public function test_credit_rejects_reused_reference_with_different_amount(): void
    {
        $user = User::factory()->create();

        $this->service->credit(
            user: $user,
            amount: 10000,
            reference: 'REFERENCE-001',
        );

        $this->expectException(InvalidArgumentException::class);

        $this->service->credit(
            user: $user,
            amount: 50000,
            reference: 'REFERENCE-001',
        );
    }

    public function test_debit_rejects_reused_reference_with_different_amount(): void
    {
        $user = User::factory()->create();

        $this->service->credit(
            user: $user,
            amount: 20000,
            reference: 'CREDIT-REFERENCE-001',
        );

        $this->service->debit(
            user: $user,
            amount: 5000,
            reference: 'REFERENCE-002',
        );

        $this->expectException(InvalidArgumentException::class);

        $this->service->debit(
            user: $user,
            amount: 8000,
            reference: 'REFERENCE-002',
        );
    }
}