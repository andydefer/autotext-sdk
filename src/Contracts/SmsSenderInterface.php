<?php

namespace Andydefer\AutotextSdk\Contracts;

use Andydefer\AutotextSdk\Dtos\TextoDto;

interface SmsSenderInterface
{
    /**
     * Envoie un texto à un device via son FCM token.
     *
     * @param TextoDto $texto
     * @param string $deviceFcmToken
     * @return bool
     */
    public function send(TextoDto $texto, string $deviceFcmToken): bool;
}
