<?php

namespace Andydefer\AutotextSdk\Services;

use Andydefer\AutotextSdk\Contracts\HttpClientInterface;
use Andydefer\AutotextSdk\Dtos\FcmMessageDto;

class FirebaseService
{
    protected HttpClientInterface $client;
    protected FirebaseAuthProvider $authProvider;
    protected FcmPayloadBuilder $payloadBuilder;
    protected string $projectId;
    protected array $config;

    public function __construct(
        HttpClientInterface $client,
        FirebaseAuthProvider $authProvider,
        FcmPayloadBuilder $payloadBuilder,
        array $config
    ) {
        $this->client = $client;
        $this->authProvider = $authProvider;
        $this->payloadBuilder = $payloadBuilder;
        $this->config = $config;
        $this->projectId = $config['project_id'] ?? '';
    }

    public function send(FcmMessageDto $messageDto, string $deviceToken): array
    {
        $token = $this->authProvider->getAccessToken($this->config);
        $payload = $this->payloadBuilder->build($messageDto);

        // Injecte le deviceToken dans le payload
        $payload['message']['token'] = $deviceToken;

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        return $this->client->post($url, [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
            'timeout' => 30,
        ]);
    }

    public function sendSmsToDevice(string $deviceToken, $texto): array
    {
        $messageDto = new FcmMessageDto(
            actionType: \Andydefer\AutotextSdk\Enums\FcmActionType::SEND_SMS,
            message: $texto->message,
            phoneNumber: $texto->phoneNumber,
            smsId: $texto->uuid,
        );

        return $this->send($messageDto, $deviceToken);
    }
}
