<?php

namespace App\Services\Payment;

final class ProviderPaymentPayloadDTO
{
    public function __construct(
        public float $transactionId,
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
