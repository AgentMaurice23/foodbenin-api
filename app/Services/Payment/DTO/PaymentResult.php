<?php

namespace App\Services\Payment\DTO;

/**
 * Represents the result returned by a payment provider.
 *
 * This DTO gives every provider the same response structure,
 * regardless of the external API being used.
 */
class PaymentResult
{
    /**
     * Create a new payment result.
     */
    public function __construct(
        public readonly bool $success,
        public readonly string $status,
        public readonly ?string $transactionReference = null,
        public readonly ?string $message = null,
        public readonly array $metadata = [],
    ) {
    }

    /**
     * Create a successful payment result.
     */
    public static function success(
        string $status,
        ?string $transactionReference = null,
        ?string $message = null,
        array $metadata = [],
    ): self {
        return new self(
            success: true,
            status: $status,
            transactionReference: $transactionReference,
            message: $message,
            metadata: $metadata,
        );
    }

    /**
     * Create a failed payment result.
     */
    public static function failure(
        string $status,
        ?string $message = null,
        array $metadata = [],
    ): self {
        return new self(
            success: false,
            status: $status,
            transactionReference: null,
            message: $message,
            metadata: $metadata,
        );
    }
}