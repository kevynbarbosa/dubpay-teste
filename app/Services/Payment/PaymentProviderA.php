<?php

namespace App\Services\Payment;

use App\DTO\ProviderPaymentPayloadDTO;
use App\DTO\ProviderPaymentResponseDTO;
use App\Exceptions\ProviderAAuthException;
use App\Models\LogTransaction;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PaymentProviderA implements PaymentProviderInterface
{
    private string $apiKey;
    private string $authToken;
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.provider_a.base_url');
        $this->apiKey = config('services.provider_a.api_key');

        $this->auth();
    }

    public function auth(): void
    {
        if (Cache::has('payment_provider_a_token')) {
            $this->authToken = Cache::get('payment_provider_a_token');
            return;
        }

        try {
            $response = Http::post($this->baseUrl . '/auth', [
                'api_key' => $this->apiKey,
            ]);

            $response->throwUnlessStatus(200);

            $this->authToken = $response->json('auth_token');

            Cache::put('payment_provider_a_token', $this->authToken);
        } catch (\Throwable $th) {
            throw new ProviderAAuthException($th->getMessage());
        }
    }

    public function requestPayment(ProviderPaymentPayloadDTO $dto): ProviderPaymentResponseDTO
    {
        try {
            $payload = [
                'transaction_id' => $dto->transactionId,
                'amount' => $dto->amount,
            ];

            $response = Http::withToken($this->authToken)->post($this->baseUrl . '/payments', $payload);

            LogTransaction::create([
                'url' => $this->baseUrl . '/payments',
                'transaction_id' => $dto->transactionId,
                'provider' => 'ProviderA',
                'payload' => json_encode($payload),
                'response_data' => $response->body(),
            ]);

            $response->throwUnlessStatus(200);

            $responseData = $response->json();

            return ProviderPaymentResponseDTO::fromArray([
                'paymentId' => $responseData['payment_id'],
                'status' => $response->status(),
            ]);
        } catch (\Throwable $th) {
            throw new Exception('Payment request failed: ' . $th->getMessage());
        }
    }
}
