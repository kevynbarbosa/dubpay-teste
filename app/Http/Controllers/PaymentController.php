<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayRequest;
use App\Http\Requests\ProviderAWebhookRequest;
use App\Http\Requests\ProviderBWebhookRequest;
use App\Services\Payment\PaymentService;
use App\Actions\PaymentProvider\ProviderAWebhookAction;
use App\Actions\PaymentProvider\ProviderBWebhookAction;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function pay(PayRequest $request)
    {
        $data = $request->validated();

        try {
            $this->paymentService->createTransaction($data);

            return response()->json(['message' => 'Payment processed successfully']);
        } catch (\Throwable $th) {
            throw $th;
            return response()->json(['error' => 'Payment processing failed'], 500);
        }
    }

    public function handleProviderAWebhook(ProviderAWebhookRequest $request)
    {
        try {
            $data = $request->validated();

            (new ProviderAWebhookAction())->execute($data);
        } catch (\Throwable $th) {
            throw $th;
            return response()->json(['error' => 'Error processing webhook'], 500);
        }
    }

    public function handleProviderBWebhook(ProviderBWebhookRequest $request)
    {
        try {
            $data = $request->validated();

            (new ProviderBWebhookAction())->execute($data);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error processing webhook'], 500);
        }
    }
}
