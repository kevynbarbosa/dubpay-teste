<?php

namespace App\Services\Payment;

use App\DTO\ProviderPaymentPayloadDTO;
use App\DTO\ProviderPaymentResponseDTO;

interface PaymentProviderInterface
{
    public function requestPayment(ProviderPaymentPayloadDTO $data): ProviderPaymentResponseDTO;
}
