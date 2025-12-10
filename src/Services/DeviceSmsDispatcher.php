<?php

namespace Andydefer\AutotextSdk\Services;

use Andydefer\AutotextSdk\Dtos\TextoDto;
use Andydefer\AutotextSdk\Dtos\AutoTextDeviceDto;
use Andydefer\AutotextSdk\Enums\AutoTextDeviceStatus;
use Andydefer\AutotextSdk\Contracts\SmsSenderInterface;

class DeviceSmsDispatcher
{
    protected SmsSenderInterface $sender; // <-- interface, pas FirebaseService

    public function __construct(SmsSenderInterface $sender)
    {
        $this->sender = $sender;
    }

    public function dispatch(TextoDto $texto, AutoTextDeviceDto $device): bool
    {
        if ($device->status !== AutoTextDeviceStatus::ONLINE) {
            throw new \InvalidArgumentException("Device {$device->id} is not online.");
        }

        if (empty($device->fcmId)) {
            throw new \InvalidArgumentException("Device {$device->id} has no FCM ID.");
        }

        return $this->sender->send($texto, $device->fcmId); // interface
    }
}
