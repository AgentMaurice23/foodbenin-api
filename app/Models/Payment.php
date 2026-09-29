<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'restaurant_id',
        'amount',
        'payment_method',
        'uuid',
        'transaction_reference',
        'status',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_method' => PaymentMethodEnum::class,
        'status' => PaymentStatusEnum::class,
        'paid_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the order associated with the payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who made the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the restaurant associated with the payment.
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

        /**
     * Get the webhook events associated with the payment.
     */
    public function webhookEvents(): HasMany
    {
        return $this->hasMany(PaymentWebhookEvent::class);
    }
}