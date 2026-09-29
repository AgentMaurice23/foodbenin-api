<?php

namespace App\Models;

use App\Enums\WalletTransactionStatusEnum;
use App\Enums\WalletTransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'status',
        'reference',
        'description',
        'metadata',
        'completed_at',
    ];

    protected $casts = [
        'type' => WalletTransactionTypeEnum::class,
        'status' => WalletTransactionStatusEnum::class,
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'completed_at' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * User propriétaire de la transaction,
     * accessible à travers le wallet.
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Wallet::class,
            'id',
            'id',
            'wallet_id',
            'user_id'
        );
    }
}