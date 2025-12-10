<?php

// src/Dtos/FcmMessageDto.php

namespace Andydefer\AutotextSdk\Dtos;

use Andydefer\AutotextSdk\Enums\FcmActionType;

class FcmMessageDto
{
    public function __construct(
        public FcmActionType $actionType,
        public string $message,
        public ?string $phoneNumber = null,
        public ?string $smsId = null,
        public ?string $timestamp = null
    ) {
        // Timestamp en ISO8601
        $this->timestamp = $timestamp ?? date('c');
    }

    /**
     * Transforme le DTO en tableau
     */
    public function toArray(): array
    {
        return [
            'action_type' => $this->actionType->value,
            'message' => $this->message,
            'phone_number' => (string) $this->phoneNumber,
            'sms_id' => (string) $this->smsId,
            'timestamp' => (string) $this->timestamp,
        ];
    }
}
