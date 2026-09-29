<?php

namespace Tests\Unit;

use App\Enums\NotificationTypeEnum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class NotificationTemplateTest extends TestCase
{
    #[Test]
    public function it_creates_order_created_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::ORDER_CREATED,
                [
                    'order_number' => 'FB-000123',
                ]
            );

        $this->assertSame(
            'Commande reçue',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000123',
            $template['message']
        );
    }

    #[Test]
    public function it_creates_order_confirmed_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::ORDER_CONFIRMED,
                [
                    'order_number' => 'FB-000124',
                ]
            );

        $this->assertSame(
            'Commande confirmée',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000124',
            $template['message']
        );
    }

    #[Test]
    public function it_creates_order_preparing_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::ORDER_PREPARING,
                [
                    'order_number' => 'FB-000125',
                ]
            );

        $this->assertSame(
            'Commande en préparation',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000125',
            $template['message']
        );
    }

    #[Test]
    public function it_creates_order_ready_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::ORDER_READY,
                [
                    'order_number' => 'FB-000126',
                ]
            );

        $this->assertSame(
            'Commande prête',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000126',
            $template['message']
        );
    }

    #[Test]
    public function it_creates_order_cancelled_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::ORDER_CANCELLED,
                [
                    'order_number' => 'FB-000127',
                ]
            );

        $this->assertSame(
            'Commande annulée',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000127',
            $template['message']
        );
    }

    #[Test]
    public function it_creates_delivery_assigned_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::DELIVERY_ASSIGNED,
                [
                    'order_number' => 'FB-000128',
                    'driver_name' => 'Jean',
                ]
            );

        $this->assertSame(
            'Livreur assigné',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000128',
            $template['message']
        );

        $this->assertStringContainsString(
            'Jean',
            $template['message']
        );
    }

    #[Test]
    public function it_creates_delivery_started_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::DELIVERY_STARTED,
                [
                    'order_number' => 'FB-000129',
                ]
            );

        $this->assertSame(
            'Livraison en cours',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000129',
            $template['message']
        );
    }

    #[Test]
    public function it_creates_delivery_completed_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::DELIVERY_COMPLETED,
                [
                    'order_number' => 'FB-000130',
                ]
            );

        $this->assertSame(
            'Livraison terminée',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000130',
            $template['message']
        );
    }

    #[Test]
    public function it_creates_delivery_failed_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::DELIVERY_FAILED,
                [
                    'order_number' => 'FB-000131',
                    'reason' => 'Client indisponible',
                ]
            );

        $this->assertSame(
            'Livraison échouée',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000131',
            $template['message']
        );

        $this->assertStringContainsString(
            'Client indisponible',
            $template['message']
        );
    }

    #[Test]
    public function it_creates_delivery_cancelled_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::DELIVERY_CANCELLED,
                [
                    'order_number' => 'FB-000132',
                ]
            );

        $this->assertSame(
            'Livraison annulée',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000132',
            $template['message']
        );
    }

    #[Test]
    public function it_creates_payment_pending_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::PAYMENT_PENDING,
                [
                    'order_number' => 'FB-000133',
                ]
            );

        $this->assertSame(
            'Paiement en attente',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000133',
            $template['message']
        );
    }

    #[Test]
    public function it_creates_payment_paid_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::PAYMENT_PAID,
                [
                    'order_number' => 'FB-000134',
                ]
            );

        $this->assertSame(
            'Paiement confirmé',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000134',
            $template['message']
        );
    }

    #[Test]
    public function it_creates_payment_failed_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::PAYMENT_FAILED,
                [
                    'order_number' => 'FB-000135',
                    'reason' => 'Solde insuffisant',
                ]
            );

        $this->assertSame(
            'Paiement échoué',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000135',
            $template['message']
        );

        $this->assertStringContainsString(
            'Solde insuffisant',
            $template['message']
        );
    }

    #[Test]
    public function it_creates_payment_refunded_template(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::PAYMENT_REFUNDED,
                [
                    'order_number' => 'FB-000136',
                ]
            );

        $this->assertSame(
            'Paiement remboursé',
            $template['title']
        );

        $this->assertStringContainsString(
            'FB-000136',
            $template['message']
        );
    }

    #[Test]
    public function it_replaces_dynamic_values(): void
    {
        $template = app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::ORDER_CREATED,
                [
                    'order_number' => 'FB-999999',
                ]
            );

        $this->assertStringNotContainsString(
            '{{order_number}}',
            $template['message']
        );

        $this->assertStringContainsString(
            'FB-999999',
            $template['message']
        );
    }

    #[Test]
    public function it_requires_order_number_when_template_needs_it(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::ORDER_CREATED
            );
    }

    #[Test]
    public function it_requires_driver_name_for_delivery_assignment(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::DELIVERY_ASSIGNED,
                [
                    'order_number' => 'FB-000137',
                ]
            );
    }

    #[Test]
    public function it_requires_reason_for_delivery_failure(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::DELIVERY_FAILED,
                [
                    'order_number' => 'FB-000138',
                ]
            );
    }

    #[Test]
    public function it_requires_reason_for_payment_failure(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        app(\App\Services\Notification\Templates\NotificationTemplate::class)
            ->make(
                NotificationTypeEnum::PAYMENT_FAILED,
                [
                    'order_number' => 'FB-000139',
                ]
            );
    }
}