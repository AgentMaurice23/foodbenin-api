<?php

namespace App\Enums;

/**
 * Available notification delivery channels.
 */
enum NotificationChannelEnum: string
{
    case DATABASE = 'database';

    case EMAIL = 'email';

    case SMS = 'sms';

    case WHATSAPP = 'whatsapp';

    case PUSH = 'push';
}