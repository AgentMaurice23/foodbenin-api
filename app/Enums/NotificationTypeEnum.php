<?php

namespace App\Enums;

/**
 * Available business notification types.
 *
 * Notification types are grouped by business domain:
 *
 * - Order lifecycle
 * - Delivery lifecycle
 * - Payment lifecycle
 */
enum NotificationTypeEnum: string
{
    /*
     * -------------------------------------------------------------
     * Order lifecycle.
     * -------------------------------------------------------------
     */

    case ORDER_CREATED = 'order_created';

    case ORDER_CONFIRMED = 'order_confirmed';

    case ORDER_PREPARING = 'order_preparing';

    case ORDER_READY = 'order_ready';

    case ORDER_ASSIGNED = 'order_assigned';

    case ORDER_PICKED_UP = 'order_picked_up';

    case ORDER_DELIVERED = 'order_delivered';

    case ORDER_CANCELLED = 'order_cancelled';


    /*
     * -------------------------------------------------------------
     * Delivery lifecycle.
     * -------------------------------------------------------------
     */

    case DELIVERY_ASSIGNED = 'delivery_assigned';

    case DELIVERY_STATUS_CHANGED = 'delivery_status_changed';

    case DELIVERY_STARTED = 'delivery_started';

    case DELIVERY_COMPLETED = 'delivery_completed';

    case DELIVERY_FAILED = 'delivery_failed';

    case DELIVERY_CANCELLED = 'delivery_cancelled';


    /*
     * -------------------------------------------------------------
     * Payment lifecycle.
     * -------------------------------------------------------------
     */

    case PAYMENT_PENDING = 'payment_pending';

    case PAYMENT_PROCESSING = 'payment_processing';

    case PAYMENT_PAID = 'payment_paid';

    case PAYMENT_FAILED = 'payment_failed';

    case PAYMENT_REFUNDED = 'payment_refunded';
}