<?php

namespace App\Services\Payment;

use App\DTO\ProviderPaymentPayloadDTO;
use App\DTO\ProviderPaymentResponseDTO;
use App\Models\LogTransaction;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PaymentProviderB implements PaymentProviderInterface
{
    private string $baseUrl;
    private string $user;
    private string $password;
    private string $authToken;

    public function __construct()
    {
        $this->baseUrl = config('services.provider_b.base_url');
        $this->user = config('services.provider_b.user');
        $this->password = config('services.provider_b.password');

        $this->auth();
    }

    public function auth()
    {
        if (Cache::has('payment_provider_b_token')) {
            $this->authToken = Cache::get('payment_provider_b_token');
        }

        try {
            $response = Http::post($this->baseUrl . '/auth', [
                'user' => $this->user,
                'password' => $this->password,
            ]);

            $response->throwUnlessStatus(200);

            Cache::put('payment_provider_b_token', $response->json('token'));

            $this->authToken = $response->json('bearer_token');
        } catch (\Throwable $th) {
            throw new Exception('Failed to authenticate with Payment Provider B: ' . $th->getMessage());
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
                'provider' => 'ProviderB',
                'payload' => json_encode($payload),
                'response_data' => $response->body(),
            ]);

            $response->throwUnlessStatus(200);

            $responseData = $response->json();

            return ProviderPaymentResponseDTO::fromArray([
                'paymentId' => $responseData['transaction_id'],
                'status' => $response->status(),
            ]);
        } catch (\Throwable $th) {
            throw new Exception('Payment request failed: ' . $th->getMessage());
        }
    }
}
