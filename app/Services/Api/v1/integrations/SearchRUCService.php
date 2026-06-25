<?php

namespace App\Services\Api\v1\integrations;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SearchRUCService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('integrations.search_apis_net.base_url'),
            'verify'   => false,
            'headers'  => [
                'Authorization' => 'Bearer ' . config('integrations.search_apis_net.token'),
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'User-Agent'    => 'Laravel/Guzzle',
            ],
            'http_errors'     => false,
            'connect_timeout' => 8,
        ]);
    }

    public function searchRUC(string $ruc): ?array
    {
        try {
            $response = $this->client->get('/v1/sunat/ruc', [
                'query' => ['numero' => $ruc],
            ]);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $responseData = json_decode($response->getBody()->getContents(), true);

            return [
                'business_name' => $responseData['razon_social'] ?? null,
                'tax_address' => $responseData['direccion'] ?? null,
                'document_number' => $responseData['numero_documento'] ?? $ruc,
            ];
        } catch (\Throwable $e) {
            Log::error("SUNAT error RUC {$ruc}: {$e->getMessage()}");
            return null;
        }
    }
}
