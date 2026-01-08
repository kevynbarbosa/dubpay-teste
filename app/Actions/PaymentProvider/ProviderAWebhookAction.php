<?php

namespace App\Actions\PaymentProvider;

use App\Models\Transaction;

class ProviderAWebhookAction
{
    public function execute(array $data): void
    {
        try {
            $status = $data['status'];
            $transactionId = $data['transaction_id'];

            Transaction::find($transactionId)->update([
                'status' => $status,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
