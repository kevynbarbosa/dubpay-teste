<?php

namespace App\Actions\PaymentProvider;

use App\Models\Transaction;

class ProviderBWebhookAction
{
    public function execute(array $data): void
    {
        try {
            $status = $data['message'];
            $transactionId = $data['transaction_id'];

            Transaction::findOrFail($transactionId)->update([
                'status' => $status,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
