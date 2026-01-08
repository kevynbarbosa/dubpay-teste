<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayRequest;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;

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

    public function handleProviderWebhook(Request $request)
    {
        // Lógica para lidar com webhooks dos provedores de pagamento
        try {
            //code...
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error processing webhook'], 500);
        }
    }
}
