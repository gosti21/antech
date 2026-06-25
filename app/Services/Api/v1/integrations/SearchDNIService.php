<?php

namespace App\Services\Api\v1\integrations;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SearchDNIService
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

    public function searchDNI(string $dni): ?array
    {
        try {
            $response = $this->client->get('/v1/reniec/dni', [
                'query' => ['numero' => $dni],
            ]);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $responseData = json_decode($response->getBody()->getContents(), true);

            return [
                'name'            => $responseData['first_name'] ?? null,
                'last_name'       => trim(
                    ($responseData['first_last_name'] ?? '') . ' ' .
                        ($responseData['second_last_name'] ?? '')
                ),
                'document_number' => $responseData['document_number'] ?? $dni,
            ];

        } catch (\Throwable $e) {
            Log::error("RENIEC error DNI {$dni}: {$e->getMessage()}");
            return null;
        }
    }
}
