<?php

namespace App\Models;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Notification extends Model
{
    use HasFactory;

    /**
     * The primary key is an UUID.
     */
    protected $keyType = 'string';

    /**
     * UUIDs are not auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
        'channel',
        'title',
        'message',
        'data',
        'notifiable_type',
        'notifiable_id',
        'read_at',
        'sent_at',
        'failed_at',
        'error_message',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'type' => NotificationTypeEnum::class,
        'channel' => NotificationChannelEnum::class,
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    /**
     * Generate UUID automatically.
     */
    protected static function booted(): void
    {
        static::creating(function (Notification $notification) {
            if (empty($notification->id)) {
                $notification->id = (string) Str::uuid();
            }
        });
    }

    /**
     * The entity associated with the notification.
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Determine whether the notification has been read.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Determine whether the notification has been sent.
     */
    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }

    /**
     * Determine whether the notification has failed.
     */
    public function hasFailed(): bool
    {
        return $this->failed_at !== null;
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        if ($this->isRead()) {
            return;
        }

        $this->update([
            'read_at' => now(),
        ]);
    }

    /**
     * Mark the notification as sent.
     */
    public function markAsSent(): void
    {
        $this->update([
            'sent_at' => now(),
            'failed_at' => null,
            'error_message' => null,
        ]);
    }

    /**
     * Mark the notification as failed.
     */
    public function markAsFailed(string $error): void
    {
        $this->update([
            'failed_at' => now(),
            'error_message' => $error,
        ]);
    }
}