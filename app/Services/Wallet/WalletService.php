<?php

namespace App\Services\Wallet;

use App\Enums\WalletTransactionStatusEnum;
use App\Enums\WalletTransactionTypeEnum;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Central service responsible for wallet operations.
 *
 * Responsibilities:
 *
 * - Create or retrieve a user's wallet.
 * - Credit a wallet.
 * - Debit a wallet.
 * - Prevent negative balances.
 * - Persist every financial operation as a transaction.
 * - Guarantee atomic balance updates.
 * - Guarantee idempotence through transaction references.
 */
class WalletService
{
    /**
     * Get the user's wallet or create it if it does not exist.
     */
    public function getOrCreateWallet(User $user): Wallet
    {
        return Wallet::query()->firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'balance' => 0,
            ]
        );
    }

    /**
     * Credit a user's wallet.
     */
    public function credit(
        User $user,
        float|int|string $amount,
        string $reference,
        ?string $description = null,
        array $metadata = [],
    ): Wallet {
        $amount = $this->validateAmount($amount);
        $reference = $this->validateReference($reference);

        return DB::transaction(function () use (
            $user,
            $amount,
            $reference,
            $description,
            $metadata,
        ) {
            $existingTransaction = $this->findExistingTransaction(
                $reference
            );

            if ($existingTransaction) {
                $this->validateIdempotentTransaction(
                    $existingTransaction,
                    WalletTransactionTypeEnum::CREDIT,
                    $amount
                );

                return $existingTransaction
                    ->wallet()
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $wallet = $this->lockOrCreateWallet($user);

            $wallet->balance = bcadd(
                (string) $wallet->balance,
                $amount,
                2
            );

            $wallet->save();

            WalletTransaction::query()->create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionTypeEnum::CREDIT,
                'amount' => $amount,
                'status' => WalletTransactionStatusEnum::COMPLETED,
                'reference' => $reference,
                'description' => $description,
                'metadata' => $metadata,
                'completed_at' => now(),
            ]);

            return $wallet->fresh();
        });
    }

    /**
     * Debit a user's wallet.
     */
    public function debit(
        User $user,
        float|int|string $amount,
        string $reference,
        ?string $description = null,
        array $metadata = [],
    ): Wallet {
        $amount = $this->validateAmount($amount);
        $reference = $this->validateReference($reference);

        return DB::transaction(function () use (
            $user,
            $amount,
            $reference,
            $description,
            $metadata,
        ) {
            $existingTransaction = $this->findExistingTransaction(
                $reference
            );

            if ($existingTransaction) {
                $this->validateIdempotentTransaction(
                    $existingTransaction,
                    WalletTransactionTypeEnum::DEBIT,
                    $amount
                );

                return $existingTransaction
                    ->wallet()
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $wallet = Wallet::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$wallet) {
                throw new InvalidArgumentException(
                    'Le portefeuille de cet utilisateur n\'existe pas.'
                );
            }

            $currentBalance = (string) $wallet->balance;

            if (bccomp($currentBalance, $amount, 2) < 0) {
                throw new InvalidArgumentException(
                    'Solde insuffisant pour effectuer cette opération.'
                );
            }

            $wallet->balance = bcsub(
                $currentBalance,
                $amount,
                2
            );

            $wallet->save();

            WalletTransaction::query()->create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionTypeEnum::DEBIT,
                'amount' => $amount,
                'status' => WalletTransactionStatusEnum::COMPLETED,
                'reference' => $reference,
                'description' => $description,
                'metadata' => $metadata,
                'completed_at' => now(),
            ]);

            return $wallet->fresh();
        });
    }

    /**
     * Find an existing transaction by its unique reference.
     */
    protected function findExistingTransaction(
        string $reference
    ): ?WalletTransaction {
        return WalletTransaction::query()
            ->where('reference', $reference)
            ->lockForUpdate()
            ->first();
    }

    /**
     * Validate that a reused reference represents the exact
     * same financial operation.
     */
    protected function validateIdempotentTransaction(
        WalletTransaction $transaction,
        WalletTransactionTypeEnum $expectedType,
        string $expectedAmount
    ): void {
        $actualType = $transaction->type;

        if ($actualType !== $expectedType) {
            throw new InvalidArgumentException(
                'Cette référence de transaction est déjà utilisée pour une autre opération.'
            );
        }

        if (
            bccomp(
                (string) $transaction->amount,
                $expectedAmount,
                2
            ) !== 0
        ) {
            throw new InvalidArgumentException(
                'Cette référence de transaction est déjà utilisée avec un montant différent.'
            );
        }
    }

    /**
     * Lock an existing wallet or create it.
     */
    protected function lockOrCreateWallet(User $user): Wallet
    {
        $wallet = Wallet::query()
            ->where('user_id', $user->id)
            ->lockForUpdate()
            ->first();

        if ($wallet) {
            return $wallet;
        }

        $wallet = Wallet::query()->create([
            'user_id' => $user->id,
            'balance' => 0,
        ]);

        return Wallet::query()
            ->whereKey($wallet->id)
            ->lockForUpdate()
            ->firstOrFail();
    }

    /**
     * Validate a transaction amount.
     */
    protected function validateAmount(
        float|int|string $amount
    ): string {
        if (!is_numeric($amount)) {
            throw new InvalidArgumentException(
                'Le montant de la transaction doit être numérique.'
            );
        }

        if ((float) $amount <= 0) {
            throw new InvalidArgumentException(
                'Le montant de la transaction doit être supérieur à zéro.'
            );
        }

        return number_format(
            (float) $amount,
            2,
            '.',
            ''
        );
    }

    /**
     * Validate a transaction reference.
     */
    protected function validateReference(
        string $reference
    ): string {
        $reference = trim($reference);

        if ($reference === '') {
            throw new InvalidArgumentException(
                'La référence de transaction est obligatoire.'
            );
        }

        return $reference;
    }
}