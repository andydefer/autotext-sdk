<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Andydefer\AutotextSdk\Dtos\TextoDto;
use Andydefer\AutotextSdk\Dtos\AutoTextDeviceDto;
use Andydefer\AutotextSdk\Services\DeviceSmsDispatcher;
use Andydefer\AutotextSdk\Contracts\SmsSenderInterface;
use Andydefer\AutotextSdk\Enums\TextoStatus;
use Andydefer\AutotextSdk\Enums\AutoTextDeviceStatus;

class DeviceSmsDispatcherTest extends TestCase
{
    public function testDispatchSmsCallsSenderInterface(): void
    {
        // Mock du sender (ex: FirebaseService implémente SmsSenderInterface)
        $senderMock = $this->createMock(SmsSenderInterface::class);

        $senderMock->expects($this->once())
            ->method('send')
            ->with(
                $this->isInstanceOf(TextoDto::class),
                $this->equalTo('fcm-123')
            )
            ->willReturn(true); // L'interface renvoie un bool

        $dispatcher = new DeviceSmsDispatcher($senderMock);

        $texto = new TextoDto(
            id: 1,
            uuid: 'uuid-1',
            message: 'Hello world',
            phoneNumber: '+33000000000',
            status: TextoStatus::PENDING,
            deviceId: 1,
            retryCount: 0,
            lastAttemptAt: null,
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $device = new AutoTextDeviceDto(
            id: 'device-1',
            apiKey: 'api-123',
            status: AutoTextDeviceStatus::ONLINE,
            fcmId: 'fcm-123',
            lastConnectedAt: date('c'),
            lastActionAt: date('c'),
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $result = $dispatcher->dispatch($texto, $device);

        $this->assertTrue($result, "Le dispatcher doit retourner true si l'envoi a réussi.");
    }

    public function testDispatchThrowsExceptionIfDeviceOffline(): void
    {
        $senderMock = $this->createMock(SmsSenderInterface::class);
        $dispatcher = new DeviceSmsDispatcher($senderMock);

        $texto = new TextoDto(
            id: 1,
            uuid: 'uuid-2',
            message: 'Hello offline',
            phoneNumber: '+33000000001',
            status: TextoStatus::PENDING,
            deviceId: 1,
            retryCount: 0,
            lastAttemptAt: null,
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $device = new AutoTextDeviceDto(
            id: 'device-2',
            apiKey: 'api-456',
            status: AutoTextDeviceStatus::OFFLINE, // offline
            fcmId: 'fcm-456',
            lastConnectedAt: date('c'),
            lastActionAt: date('c'),
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $this->expectException(\InvalidArgumentException::class);
        $dispatcher->dispatch($texto, $device);
    }

    public function testDispatchThrowsExceptionIfDeviceHasNoFcmId(): void
    {
        $senderMock = $this->createMock(SmsSenderInterface::class);
        $dispatcher = new DeviceSmsDispatcher($senderMock);

        $texto = new TextoDto(
            id: 1,
            uuid: 'uuid-3',
            message: 'Hello no FCM',
            phoneNumber: '+33000000002',
            status: TextoStatus::PENDING,
            deviceId: 1,
            retryCount: 0,
            lastAttemptAt: null,
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $device = new AutoTextDeviceDto(
            id: 'device-3',
            apiKey: 'api-789',
            status: AutoTextDeviceStatus::ONLINE,
            fcmId: null, // pas de FCM ID
            lastConnectedAt: date('c'),
            lastActionAt: date('c'),
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $this->expectException(\InvalidArgumentException::class);
        $dispatcher->dispatch($texto, $device);
    }
}
