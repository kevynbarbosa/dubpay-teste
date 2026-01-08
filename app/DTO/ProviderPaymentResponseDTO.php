<?php

namespace App\DTO;

final class ProviderPaymentResponseDTO
{
    public function __construct(
        public readonly string $status,
        public readonly string $transactionId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            transactionId: $data['transaction_id'] ?? $data['transactionId'] ?? $data['paymentId'],
        );
    }
}
