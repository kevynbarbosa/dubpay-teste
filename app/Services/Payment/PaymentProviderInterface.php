<?php

namespace App\Services\Payment;

interface PaymentProviderInterface
{
    public function makeTransaction(array $data): ProviderPaymentResponseDTO;
}
