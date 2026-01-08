<?php


namespace App\Services\Payment;


class PaymentService
{
    private PaymentProviderInterface $provider;

    public function pay(array $data): ProviderPaymentResponseDTO
    {
        // Determine the provider based on a strategy (e.g., config, data)
        // $this->provider = new PaymentProviderA(); // or another provider

        // Save ProviderPaymentResponseDTO
        return $this->provider->makeTransaction($data);
    }
}
