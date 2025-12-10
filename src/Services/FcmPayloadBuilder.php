<?php

namespace Andydefer\AutotextSdk\Services;

use Andydefer\AutotextSdk\Dtos\FcmMessageDto;

class FcmPayloadBuilder
{
    public function build(FcmMessageDto $dto): array
    {
        return [
            'message' => [
                'token' => $dto->deviceToken ?? '',
                'data' => $this->prepareData($dto->toArray()),
                'android' => ['priority' => 'high'],
                'apns' => [
                    'payload' => ['aps' => ['content-available' => 1]],
                    'headers' => ['apns-priority' => '5'],
                ],
            ],
        ];
    }

    private function prepareData(array $data): array
    {
        return array_map(fn($v) => (string) $v, $data);
    }
}
