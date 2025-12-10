<?php

namespace Andydefer\AutotextSdk\Dtos;

use Andydefer\AutotextSdk\Enums\TextoStatus;

class TextoDto
{
    public function __construct(
        public int $id,
        public string $uuid,
        public string $message,
        public string $phoneNumber,
        public TextoStatus $status,
        public int $deviceId,
        public int $retryCount,
        public ?string $lastAttemptAt, // ISO8601 string or null. Example: "2025-12-10T13:45:30+00:00"
        public string $createdAt,      // ISO8601 string. Example: "2025-12-10T13:45:30+00:00"
        public string $updatedAt,      // ISO8601 string. Example: "2025-12-10T13:45:30+00:00"
    ) {}

    /**
     * Create DTO from a raw associative array.
     * This method is framework-agnostic and expects date fields as ISO8601 strings.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            uuid: (string) $data['uuid'],
            message: (string) $data['message'],
            phoneNumber: (string) $data['phone_number'],
            status: TextoStatus::from($data['status']),
            deviceId: (int) $data['device_id'],
            retryCount: (int) $data['retry_count'],
            lastAttemptAt: $data['last_attempt_at'] ?? null, // ISO8601 or null
            createdAt: (string) $data['created_at'],          // ISO8601
            updatedAt: (string) $data['updated_at'],          // ISO8601
        );
    }

    /**
     * Convert DTO into an array suitable for serialization or JSON.
     * All date fields remain ISO8601 strings.
     */
    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'uuid'             => $this->uuid,
            'message'          => $this->message,
            'phone_number'     => $this->phoneNumber,
            'status'           => $this->status,
            'device_id'        => $this->deviceId,
            'retry_count'      => $this->retryCount,
            'last_attempt_at'  => $this->lastAttemptAt, // ISO8601 or null
            'created_at'       => $this->createdAt,     // ISO8601
            'updated_at'       => $this->updatedAt,     // ISO8601
        ];
    }
}
