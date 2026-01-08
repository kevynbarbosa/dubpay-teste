<?php

namespace App\Actions\PaymentProvider;

use App\Models\Transaction;
use App\Models\WebhookEvent;

class ProviderBWebhookAction
{
    public function execute(array $data): void
    {
        try {
            $type = $data['type'];
            $transactionId = $data['transaction_id'];
            $dateCreated = $data['date_created'];
            $payloadHash = $this->payloadHash($data);

            Transaction::findOrFail($transactionId);

            $latestDateCreated = WebhookEvent::where('provider', 'ProviderB')
                ->where('transaction_id', $transactionId)
                ->max('date_created');

            if ($latestDateCreated && $dateCreated <= $latestDateCreated) {
                return;
            }

            $alreadyProcessed = WebhookEvent::where('provider', 'ProviderB')
                ->where('transaction_id', $transactionId)
                ->where('payload_hash', $payloadHash)
                ->exists();

            if ($alreadyProcessed) {
                return;
            }

            Transaction::find($transactionId)->update([
                'status' => $type,
            ]);

            WebhookEvent::create([
                'provider' => 'ProviderB',
                'transaction_id' => $transactionId,
                'type' => $type,
                'date_created' => $dateCreated,
                'payload_hash' => $payloadHash,
                'payload' => $data,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function payloadHash(array $data): string
    {
        $normalized = $this->normalizePayload($data);

        return hash('sha256', json_encode($normalized));
    }

    private function normalizePayload(array $data): array
    {
        ksort($data);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->normalizePayload($value);
            }
        }

        return $data;
    }
}
