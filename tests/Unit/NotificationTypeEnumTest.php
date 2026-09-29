<?php

namespace Tests\Unit;

use App\Enums\NotificationTypeEnum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationTypeEnumTest extends TestCase
{
    #[Test]
    public function notification_type_enum_contains_all_order_types(): void
    {
        $this->assertSame(
            'order_created',
            NotificationTypeEnum::ORDER_CREATED->value
        );

        $this->assertSame(
            'order_confirmed',
            NotificationTypeEnum::ORDER_CONFIRMED->value
        );

        $this->assertSame(
            'order_preparing',
            NotificationTypeEnum::ORDER_PREPARING->value
        );

        $this->assertSame(
            'order_ready',
            NotificationTypeEnum::ORDER_READY->value
        );

        $this->assertSame(
            'order_assigned',
            NotificationTypeEnum::ORDER_ASSIGNED->value
        );

        $this->assertSame(
            'order_picked_up',
            NotificationTypeEnum::ORDER_PICKED_UP->value
        );

        $this->assertSame(
            'order_delivered',
            NotificationTypeEnum::ORDER_DELIVERED->value
        );

        $this->assertSame(
            'order_cancelled',
            NotificationTypeEnum::ORDER_CANCELLED->value
        );
    }

    #[Test]
    public function notification_type_enum_contains_all_delivery_types(): void
    {
        $this->assertSame(
            'delivery_assigned',
            NotificationTypeEnum::DELIVERY_ASSIGNED->value
        );

        $this->assertSame(
            'delivery_status_changed',
            NotificationTypeEnum::DELIVERY_STATUS_CHANGED->value
        );

        $this->assertSame(
            'delivery_started',
            NotificationTypeEnum::DELIVERY_STARTED->value
        );

        $this->assertSame(
            'delivery_completed',
            NotificationTypeEnum::DELIVERY_COMPLETED->value
        );

        $this->assertSame(
            'delivery_failed',
            NotificationTypeEnum::DELIVERY_FAILED->value
        );

        $this->assertSame(
            'delivery_cancelled',
            NotificationTypeEnum::DELIVERY_CANCELLED->value
        );
    }

    #[Test]
    public function notification_type_enum_contains_all_payment_types(): void
    {
        $this->assertSame(
            'payment_pending',
            NotificationTypeEnum::PAYMENT_PENDING->value
        );

        $this->assertSame(
            'payment_processing',
            NotificationTypeEnum::PAYMENT_PROCESSING->value
        );

        $this->assertSame(
            'payment_paid',
            NotificationTypeEnum::PAYMENT_PAID->value
        );

        $this->assertSame(
            'payment_failed',
            NotificationTypeEnum::PAYMENT_FAILED->value
        );

        $this->assertSame(
            'payment_refunded',
            NotificationTypeEnum::PAYMENT_REFUNDED->value
        );
    }

    #[Test]
    public function notification_type_enum_has_expected_number_of_cases(): void
    {
        $this->assertCount(
            19,
            NotificationTypeEnum::cases()
        );
    }
}