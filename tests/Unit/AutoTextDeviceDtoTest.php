<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Andydefer\AutotextSdk\Dtos\AutoTextDeviceDto;
use Andydefer\AutotextSdk\Enums\AutoTextDeviceStatus;

class AutoTextDeviceDtoTest extends TestCase
{
    public function testAutoTextDeviceDtoCanBeCreated(): void
    {
        $device = new AutoTextDeviceDto(
            id: 'device-1',
            apiKey: 'api-123',
            status: AutoTextDeviceStatus::ONLINE,
            fcmId: 'fcm-abc',
            lastConnectedAt: date('c'),
            lastActionAt: date('c'),
            createdAt: date('c'),
            updatedAt: date('c'),
            isRecentlyConnected: true,
            isRecentlyActive: true,
            successCount: 10,
            failedCount: 2,
            successRate: 83
        );

        $this->assertEquals('device-1', $device->id);
        $this->assertTrue($device->isRecentlyConnected);
        $this->assertEquals(83, $device->successRate);

        $array = $device->toArray();
        $this->assertIsArray($array);
        $this->assertEquals('fcm-abc', $array['fcm_id']);
    }
}
