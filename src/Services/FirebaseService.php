<?php

namespace Andydefer\AutotextSdk\Services;

use Andydefer\AutotextSdk\Dtos\TextoDto;
use Andydefer\AutotextSdk\Dtos\FcmMessageDto;
use Andydefer\AutotextSdk\Enums\FcmActionType;
use GuzzleHttp\Client;

class FirebaseService
{
    protected Client $client;
    protected string $projectId;
    protected string $accessToken;

    /**
     * @param array $config Configuration Firebase (decoded JSON)
     */
    public function __construct(array $config)
    {
        $this->client = new Client();
        $this->projectId = $config['project_id'] ?? '';
        $this->accessToken = $this->getAccessToken($config);
    }

    /**
     * Crée un JWT et récupère l'access token OAuth2.
     */
    private function getAccessToken(array $data): string
    {
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $now = time();
        $claimSet = base64_encode(json_encode([
            'iss' => $data['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $data['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $signature = '';
        openssl_sign($header . '.' . $claimSet, $signature, $data['private_key'], 'SHA256');
        $jwt = $header . '.' . $claimSet . '.' . base64_encode($signature);

        $res = $this->client->post($data['token_uri'], [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ],
        ]);

        $resData = json_decode($res->getBody()->getContents(), true);

        return $resData['access_token'] ?? '';
    }

    /**
     * Envoie un message FCM à un device.
     */
    public function sendMessage(string $deviceToken, FcmMessageDto $messageDto): array
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $deviceToken,
                'data' => $this->prepareData($messageDto->toArray()),
                'android' => ['priority' => 'high'],
                'apns' => [
                    'payload' => ['aps' => ['content-available' => 1]],
                    'headers' => ['apns-priority' => '5'],
                ],
            ],
        ];

        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
            'timeout' => 30,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Envoie un SMS à un device en utilisant TextoDto.
     */
    public function sendSmsToDevice(string $deviceToken, TextoDto $texto): array
    {
        $messageDto = new FcmMessageDto(
            actionType: FcmActionType::SEND_SMS,
            message: $texto->message,
            phoneNumber: $texto->phoneNumber,
            smsId: $texto->uuid,
        );

        return $this->sendMessage($deviceToken, $messageDto);
    }

    /**
     * Force toutes les valeurs du tableau à être des strings (obligatoire pour FCM).
     */
    private function prepareData(array $data): array
    {
        return array_map(fn($v) => (string) $v, $data);
    }
}
