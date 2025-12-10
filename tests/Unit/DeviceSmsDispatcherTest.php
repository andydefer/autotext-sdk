<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Andydefer\AutotextSdk\Dtos\TextoDto;
use Andydefer\AutotextSdk\Dtos\AutoTextDeviceDto;
use Andydefer\AutotextSdk\Services\DeviceSmsDispatcher;
use Andydefer\AutotextSdk\Services\FirebaseService;
use Andydefer\AutotextSdk\Enums\TextoStatus;
use Andydefer\AutotextSdk\Enums\AutoTextDeviceStatus;

class DeviceSmsDispatcherTest extends TestCase
{
    public function testDispatchSmsCallsFirebaseService(): void
    {
        // Mock du service Firebase
        $firebaseMock = $this->createMock(FirebaseService::class);

        $firebaseMock->expects($this->once())
            ->method('sendSmsToDevice')
            ->with(
                $this->equalTo('fcm-123'),
                $this->isInstanceOf(TextoDto::class)
            )
            ->willReturn(['success' => true]); // Firebase renvoie un tableau

        $dispatcher = new DeviceSmsDispatcher($firebaseMock);

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

        // Le dispatch retourne un bool
        $result = $dispatcher->dispatch($texto, $device);

        $this->assertTrue($result, "Le dispatcher doit retourner true si l'envoi a réussi.");
    }
}
