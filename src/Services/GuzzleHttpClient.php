<?php

namespace Andydefer\AutotextSdk\Services;

use Andydefer\AutotextSdk\Contracts\HttpClientInterface;
use Andydefer\AutotextSdk\Dtos\HttpResponseDto;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GuzzleHttpClient implements HttpClientInterface
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function post(string $url, array $options): HttpResponseDto
    {
        try {
            $response = $this->client->post($url, $options);

            return new HttpResponseDto(
                statusCode: $response->getStatusCode(),
                data: json_decode($response->getBody()->getContents(), true),
                error: null
            );
        } catch (RequestException $e) {

            $status = $e->hasResponse()
                ? $e->getResponse()->getStatusCode()
                : 0;

            return new HttpResponseDto(
                statusCode: $status,
                data: null, // ❗ Toujours null en cas d’erreur, comme demandé
                error: $e->getMessage()
            );
        }
    }
}
