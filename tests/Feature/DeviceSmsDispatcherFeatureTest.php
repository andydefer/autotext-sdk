<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use Andydefer\AutotextSdk\Dtos\TextoDto;
use Andydefer\AutotextSdk\Dtos\DeviceDto;
use Andydefer\AutotextSdk\Enums\TextoStatus;
use Andydefer\AutotextSdk\Services\DeviceSmsDispatcher;
use Andydefer\AutotextSdk\Contracts\SmsSenderInterface;
use Andydefer\AutotextSdk\Enums\DeviceStatus;

class DeviceSmsDispatcherFeatureTest extends TestCase
{
    public function testDispatchSmsSuccess(): void
    {
        $senderMock = $this->createMock(SmsSenderInterface::class);

        $senderMock->expects($this->once())
            ->method('send')
            ->willReturn(true);

        $dispatcher = new DeviceSmsDispatcher($senderMock);

        $texto = new TextoDto(
            id: 1,
            uuid: uniqid('sms_'),
            message: 'Message de test',
            phoneNumber: '+33000000000',
            status: TextoStatus::PENDING,
            deviceId: 1,
            retryCount: 0,
            lastAttemptAt: null,
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $device = new DeviceDto(
            id: 'device-1',
            apiKey: 'api-123',
            status: DeviceStatus::ONLINE,
            fcmId: 'fcm-token-123',
            lastConnectedAt: date('c'),
            lastActionAt: date('c'),
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $result = $dispatcher->dispatch($texto, $device);

        $this->assertTrue($result);
    }

    public function testDispatchThrowsExceptionIfDeviceOffline(): void
    {
        $senderMock = $this->createMock(SmsSenderInterface::class);
        $dispatcher = new DeviceSmsDispatcher($senderMock);

        $texto = new TextoDto(
            id: 1,
            uuid: uniqid('sms_'),
            message: 'Message offline',
            phoneNumber: '+33000000001',
            status: TextoStatus::PENDING,
            deviceId: 1,
            retryCount: 0,
            lastAttemptAt: null,
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $device = new DeviceDto(
            id: 'device-2',
            apiKey: 'api-456',
            status: DeviceStatus::OFFLINE,
            fcmId: 'fcm-token-456',
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
            uuid: uniqid('sms_'),
            message: 'Message sans FCM',
            phoneNumber: '+33000000002',
            status: TextoStatus::PENDING,
            deviceId: 1,
            retryCount: 0,
            lastAttemptAt: null,
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $device = new DeviceDto(
            id: 'device-3',
            apiKey: 'api-789',
            status: DeviceStatus::ONLINE,
            fcmId: null,
            lastConnectedAt: date('c'),
            lastActionAt: date('c'),
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $this->expectException(\InvalidArgumentException::class);
        $dispatcher->dispatch($texto, $device);
    }

    public function testMultipleDispatches(): void
    {
        $senderMock = $this->createMock(SmsSenderInterface::class);

        $senderMock->expects($this->exactly(2))
            ->method('send')
            ->willReturn(true);

        $dispatcher = new DeviceSmsDispatcher($senderMock);

        $device = new DeviceDto(
            id: 'device-4',
            apiKey: 'api-000',
            status: DeviceStatus::ONLINE,
            fcmId: 'fcm-token-000',
            lastConnectedAt: date('c'),
            lastActionAt: date('c'),
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $texto1 = new TextoDto(
            id: 1,
            uuid: uniqid('sms_'),
            message: 'Premier message',
            phoneNumber: '+33000000003',
            status: TextoStatus::PENDING,
            deviceId: 4,
            retryCount: 0,
            lastAttemptAt: null,
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $texto2 = new TextoDto(
            id: 2,
            uuid: uniqid('sms_'),
            message: 'Deuxième message',
            phoneNumber: '+33000000004',
            status: TextoStatus::PENDING,
            deviceId: 4,
            retryCount: 0,
            lastAttemptAt: null,
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $this->assertTrue($dispatcher->dispatch($texto1, $device));
        $this->assertTrue($dispatcher->dispatch($texto2, $device));
    }
}
