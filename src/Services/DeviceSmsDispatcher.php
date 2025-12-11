<?php

namespace Andydefer\AutotextSdk\Services;

use Andydefer\AutotextSdk\Dtos\TextoDto;
use Andydefer\AutotextSdk\Dtos\DeviceDto;
use Andydefer\AutotextSdk\Enums\DeviceStatus;
use Andydefer\AutotextSdk\Contracts\SmsSenderInterface;

class DeviceSmsDispatcher
{
    protected SmsSenderInterface $sender; // <-- interface, pas FirebaseService

    public function __construct(SmsSenderInterface $sender)
    {
        $this->sender = $sender;
    }

    public function dispatch(TextoDto $texto, DeviceDto $device): bool
    {
        if ($device->status !== DeviceStatus::ONLINE) {
            throw new \InvalidArgumentException("Device {$device->id} is not online.");
        }

        if (empty($device->fcmId)) {
            throw new \InvalidArgumentException("Device {$device->id} has no FCM ID.");
        }

        return $this->sender->send($texto, $device->fcmId); // interface
    }
}
