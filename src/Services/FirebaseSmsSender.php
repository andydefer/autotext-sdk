<?php

namespace Andydefer\AutotextSdk\Services;

use Andydefer\AutotextSdk\Contracts\SmsSenderInterface;
use Andydefer\AutotextSdk\Dtos\TextoDto;

class FirebaseSmsSender implements SmsSenderInterface
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function send(TextoDto $texto, string $deviceFcmId): bool
    {
        $response = $this->firebaseService->sendSmsToDevice($deviceFcmId, $texto);
        return $response->isSuccess();
    }
}
