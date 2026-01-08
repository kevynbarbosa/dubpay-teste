<?php

namespace App\Services\Payment;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PaymentProviderA implements PaymentProviderInterface
{
    private string $apiKey;
    private string $authToken;

    public function __construct()
    {
        $this->apiKey = config('services.payment_provider_a.api_key');

        $this->auth();
    }

    public function auth(): void
    {
        if (Cache::has('payment_provider_a_token')) {
            $this->authToken = Cache::get('payment_provider_a_token');
        }

        try {
            $response = Http::fake([
                'https://api.providerA.com/*' => Http::response(['token' => md5(time())], 200),
            ]);

            $response->throwUnlessSuccessful();

            Cache::put('payment_provider_a_token', $response->json('token'));

            $this->authToken = $response->json('token');
        } catch (\Throwable $th) {
            throw new Exception('Failed to authenticate with Payment Provider A');
        }
    }

    public function makeTransaction(array $data): ProviderPaymentResponseDTO
    {
        return ProviderPaymentResponseDTO::fromArray([
            'status' => 200,
            'id_transacao' => uniqid('provA_'),
        ]);
    }
}
