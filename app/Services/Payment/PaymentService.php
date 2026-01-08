<?php


namespace App\Services\Payment;

use App\Enums\TransactionStatusEnum;
use App\Models\Transaction;
use App\DTO\ProviderPaymentPayloadDTO;
use App\DTO\ProviderPaymentResponseDTO;

class PaymentService
{
    private PaymentProviderInterface $provider;

    private function setProvider(string $providerName): void
    {
        /*  Aqui com mais tempo, poderíamos implementar um Strategy Pattern 
            Utilizando disponibilidade/custo/etc para escolher o provider */
        match ($providerName) {
            'ProviderA' => $this->provider = new PaymentProviderA(),
            'ProviderB' => $this->provider = new PaymentProviderB(),
            default => throw new \Exception('Unsupported payment provider'),
        };
    }

    public function createTransaction(array $data): ProviderPaymentResponseDTO
    {
        if (Transaction::where('order_id', $data['order_id'])->exists()) {
            throw new \Exception('Transaction already exists for this order');
        }

        $this->setProvider($data['provider']);

        try {
            $transaction = Transaction::create([
                'order_id' => $data['order_id'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'provider' => $data['provider'],
                'status' => TransactionStatusEnum::CREATED,
            ]);
        } catch (\Throwable $th) {
            throw new \Exception('Failed to create transaction: ' . $th->getMessage());
        }

        $providerPayload = new ProviderPaymentPayloadDTO(
            transactionId: $transaction->id,
            amount: $data['amount'],
            currency: $data['currency'],
        );

        $response = $this->provider->requestPayment($providerPayload);

        return $response;
    }
}
