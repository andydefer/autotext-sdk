<?php

namespace Andydefer\AutotextSdk\Services;

use Andydefer\AutotextSdk\Services\Contracts\HttpClientInterface;
use GuzzleHttp\Client;

class GuzzleHttpClient implements HttpClientInterface
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function post(string $url, array $options): array
    {
        $response = $this->client->post($url, $options);
        return json_decode($response->getBody()->getContents(), true);
    }
}
