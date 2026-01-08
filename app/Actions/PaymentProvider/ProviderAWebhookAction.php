<?php

namespace App\Actions\PaymentProvider;

use App\Models\Transaction;
use App\Models\WebhookEvent;
use Illuminate\Support\Facades\Log;

class ProviderAWebhookAction
{
    public function execute(array $data): void
    {
        try {
            $type = $data['type'];
            $transactionId = $data['transaction_id'];
            $dateCreated = $data['date_created'];
            $payloadHash = $this->payloadHash($data);

            Transaction::findOrFail($transactionId);

            $latestDateCreated = WebhookEvent::where('provider', 'ProviderA')
                ->where('transaction_id', $transactionId)
                ->max('date_created');

            if ($latestDateCreated && $dateCreated <= $latestDateCreated) {
                return;
            }

            $alreadyProcessed = WebhookEvent::where('provider', 'ProviderA')
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
                'provider' => 'ProviderA',
                'transaction_id' => $transactionId,
                'type' => $type,
                'date_created' => $dateCreated,
                'payload_hash' => $payloadHash,
                'payload' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('ProviderA webhook processing failed.', [
                'provider' => 'ProviderA',
                'transaction_id' => $data['transaction_id'] ?? null,
                'date_created' => $data['date_created'] ?? null,
                'payload_hash' => $payloadHash ?? null,
                'exception' => $th,
            ]);

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
