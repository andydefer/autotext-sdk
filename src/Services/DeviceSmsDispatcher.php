<?php

namespace Andydefer\AutotextSdk\Services;

use Andydefer\AutotextSdk\Dtos\AutoTextDeviceDto;
use Andydefer\AutotextSdk\Dtos\TextoDto;
use Andydefer\AutotextSdk\Enums\AutoTextDeviceStatus;
use Andydefer\AutotextSdk\Services\FirebaseService;

class DeviceSmsDispatcher
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Dispatch a TextoDto to a single online device.
     */
    public function dispatch(TextoDto $texto, AutoTextDeviceDto $device): bool
    {
        if ($device->status !== AutoTextDeviceStatus::ONLINE) {
            throw new \InvalidArgumentException("Device {$device->id} is not online.");
        }

        if (empty($device->fcmId)) {
            throw new \InvalidArgumentException("Device {$device->id} has no FCM ID.");
        }

        // Envoi via Firebase au device
        $response = $this->firebaseService->sendSmsToDevice($device->fcmId, $texto);

        // Optionnel : retourner true si succès, false sinon
        return $response['success'] ?? false;
    }
}
