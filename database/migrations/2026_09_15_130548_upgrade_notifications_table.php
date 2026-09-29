<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Upgrade the existing notifications table.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            /*
             * Existing Laravel notification columns:
             *
             * - id
             * - type
             * - notifiable_type
             * - notifiable_id
             * - data
             * - read_at
             * - timestamps
             *
             * We keep them untouched for compatibility.
             */

            $table->string('channel', 50)
                ->nullable()
                ->after('type');

            $table->string('title')
                ->nullable()
                ->after('channel');

            $table->text('message')
                ->nullable()
                ->after('title');

            $table->timestamp('sent_at')
                ->nullable()
                ->after('read_at');

            $table->timestamp('failed_at')
                ->nullable()
                ->after('sent_at');

            $table->text('error_message')
                ->nullable()
                ->after('failed_at');

            $table->index([
                'type',
                'channel',
            ]);
        });
    }

    /**
     * Reverse the notification table upgrade.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex([
                'notifications_type_channel_index',
            ]);

            $table->dropColumn([
                'channel',
                'title',
                'message',
                'sent_at',
                'failed_at',
                'error_message',
            ]);
        });
    }
};