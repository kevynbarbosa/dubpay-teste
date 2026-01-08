<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Cache;

class PaymentProviderB implements PaymentProviderInterface
{
    private string $userId;
    private string $password;

    public function __construct()
    {
        $this->userId = config('services.payment_provider_b.user_id');
        $this->password = config('services.payment_provider_b.password');
    }

    public function auth()
    {
        Cache::getKey('payment_provider_b_auth_token');
    }

    public function validateCredentials(): bool
    {
        return !empty($this->userId) && !empty($this->password);
    }

    public function makeTransaction(array $data): ProviderPaymentResponseDTO
    {
        return ProviderPaymentResponseDTO::fromArray([
            'status' => 'success',
            'provider' => 'PaymentProviderB',
            'transactionId' => uniqid('provB_'),
            'amount' => $data['amount'],
            'currency' => $data['currency'],
        ]);
    }
}
