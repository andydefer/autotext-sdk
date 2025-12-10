<?php

namespace Andydefer\AutotextSdk\Dtos;

use Andydefer\AutotextSdk\Enums\AutoTextDeviceStatus;

class AutoTextDeviceDto
{
    public function __construct(
        public string $id, // uuid
        public string $apiKey,
        public AutoTextDeviceStatus $status,
        public ?string $fcmId,
        public ?string $lastConnectedAt, // ISO8601 string or null. Example: "2025-12-10T13:45:30+00:00"
        public ?string $lastActionAt,    // ISO8601 string or null
        public string $createdAt,        // ISO8601 string
        public string $updatedAt,        // ISO8601 string
        public bool $isRecentlyConnected = false,
        public bool $isRecentlyActive = false,
        public int $successCount = 0,
        public int $failedCount = 0,
        public int $successRate = 0,
    ) {}

    /**
     * Crée un DTO à partir d'un tableau associatif.
     * Toutes les dates doivent être fournies sous forme de chaînes ISO8601.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) $data['id'],
            apiKey: (string) $data['api_key'],
            status: AutoTextDeviceStatus::from($data['status']),
            fcmId: $data['fcm_id'] ?? null,
            lastConnectedAt: $data['last_connected_at'] ?? null, // ISO8601 or null
            lastActionAt: $data['last_action_at'] ?? null,       // ISO8601 or null
            createdAt: (string) $data['created_at'],             // ISO8601
            updatedAt: (string) $data['updated_at'],             // ISO8601
            isRecentlyConnected: $data['is_recently_connected'] ?? false,
            isRecentlyActive: $data['is_recently_active'] ?? false,
            successCount: (int) ($data['success_count'] ?? 0),
            failedCount: (int) ($data['failed_count'] ?? 0),
            successRate: (int) ($data['success_rate'] ?? 0),
        );
    }

    /**
     * Convertit le DTO en tableau.
     * Les dates restent au format ISO8601 pour sérialisation ou transport.
     */
    public function toArray(): array
    {
        return [
            'id'                  => $this->id,
            'api_key'             => $this->apiKey,
            'status'              => $this->status->value,
            'fcm_id'              => $this->fcmId,
            'last_connected_at'   => $this->lastConnectedAt, // ISO8601 or null
            'last_action_at'      => $this->lastActionAt,    // ISO8601 or null
            'created_at'          => $this->createdAt,       // ISO8601
            'updated_at'          => $this->updatedAt,       // ISO8601
            'is_recently_connected' => $this->isRecentlyConnected,
            'is_recently_active'   => $this->isRecentlyActive,
            'success_count'        => $this->successCount,
            'failed_count'         => $this->failedCount,
            'success_rate'         => $this->successRate,
        ];
    }
}
