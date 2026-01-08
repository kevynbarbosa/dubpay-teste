<?php

namespace App\DTO;

final class ProviderPaymentPayloadDTO
{
    public function __construct(
        public string $transactionId,
        public float $amount,
        public string $currency,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            transactionId: $data['transaction_id'],
            amount: $data['amount'],
            currency: $data['currency'] ?? 'BRL',
        );
    }
}
