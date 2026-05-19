<?php

namespace App\Services\OpenFinance;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PluggyClient
{
    public function createConnectToken(string $clientUserId): string
    {
        $response = $this->request()
            ->post('/connect_token', [
                'options' => [
                    'clientUserId' => $clientUserId,
                ],
            ])
            ->throw()
            ->json();

        return (string) data_get($response, 'accessToken');
    }

    public function fetchItem(string $itemId): array
    {
        return $this->request()
            ->get("/items/{$itemId}")
            ->throw()
            ->json();
    }

    public function fetchAccounts(string $itemId): array
    {
        $payload = $this->request()
            ->get('/accounts', ['itemId' => $itemId])
            ->throw()
            ->json();

        return $this->extractCollection($payload);
    }

    public function fetchTransactions(string $accountId): array
    {
        $payload = $this->request()
            ->get('/transactions', ['accountId' => $accountId])
            ->throw()
            ->json();

        return $this->extractCollection($payload);
    }

    private function request(): PendingRequest
    {
        $request = $this->baseRequest()
            ->baseUrl(config('services.pluggy.base_url'))
            ->acceptJson()
            ->withHeaders([
                'X-API-KEY' => $this->apiKey(),
            ]);

        return $request;
    }

    private function apiKey(): string
    {
        $configuredKey = config('services.pluggy.api_key');

        if (filled($configuredKey)) {
            return $configuredKey;
        }

        return Cache::remember('pluggy_api_key', now()->addMinutes(50), function (): string {
            $request = $this->baseRequest()
                ->baseUrl(config('services.pluggy.base_url'))
                ->acceptJson();

            $response = $request
                ->post('/auth', [
                    'clientId' => config('services.pluggy.client_id'),
                    'clientSecret' => config('services.pluggy.client_secret'),
                ])
                ->throw()
                ->json();

            $apiKey = (string) data_get($response, 'apiKey');

            if ($apiKey === '') {
                throw new RuntimeException('Falha ao obter API Key da Pluggy.');
            }

            return $apiKey;
        });
    }

    private function baseRequest(): PendingRequest
    {
        $request = Http::timeout(30);
        $caBundle = config('services.pluggy.ca_bundle');

        if (! config('services.pluggy.verify_ssl')) {
            return $request->withoutVerifying();
        }

        if (is_string($caBundle) && $caBundle !== '') {
            return $request->withOptions(['verify' => $caBundle]);
        }

        return $request;
    }

    private function extractCollection(array $payload): array
    {
        $results = data_get($payload, 'results');

        if (is_array($results)) {
            return $results;
        }

        if (array_is_list($payload)) {
            return $payload;
        }

        return [];
    }
}
