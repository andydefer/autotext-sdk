<?php

namespace Andydefer\AutotextSdk\Core;

use Andydefer\AutotextSdk\Contracts\HttpClientInterface;
use Andydefer\AutotextSdk\Contracts\SmsSenderInterface;
use Andydefer\AutotextSdk\Services\DeviceSmsDispatcher;
use Andydefer\AutotextSdk\Services\FirebaseService;
use Andydefer\AutotextSdk\Services\FirebaseSmsSender;
use Andydefer\AutotextSdk\Services\FirebaseAuthProvider;
use Andydefer\AutotextSdk\Services\FcmPayloadBuilder;

class NotificationFactory
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private FirebaseAuthProvider $authProvider,
        private FcmPayloadBuilder $payloadBuilder,
        private array $config
    ) {}

    public function makeFirebaseService(): FirebaseService
    {
        return new FirebaseService(
            client: $this->httpClient,
            authProvider: $this->authProvider,
            payloadBuilder: $this->payloadBuilder,
            config: $this->config,
        );
    }

    public function makeSmsSender(): SmsSenderInterface
    {
        return new FirebaseSmsSender(
            $this->makeFirebaseService()
        );
    }

    public function makeDispatcher(): DeviceSmsDispatcher
    {
        return new DeviceSmsDispatcher(
            $this->makeSmsSender()
        );
    }
}
