<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Andydefer\AutotextSdk\Dtos\TextoDto;
use Andydefer\AutotextSdk\Enums\TextoStatus;

class TextoDtoTest extends TestCase
{
    public function testTextoDtoCanBeCreated(): void
    {
        $texto = new TextoDto(
            id: 1,
            uuid: 'uuid-123',
            message: 'Bonjour',
            phoneNumber: '+33000000000',
            status: TextoStatus::PENDING,
            deviceId: 10,
            retryCount: 0,
            lastAttemptAt: null,
            createdAt: date('c'),
            updatedAt: date('c')
        );

        $this->assertEquals(1, $texto->id);
        $this->assertEquals('uuid-123', $texto->uuid);
        $this->assertEquals(TextoStatus::PENDING, $texto->status);
        $this->assertNull($texto->lastAttemptAt);

        $array = $texto->toArray();
        $this->assertIsArray($array);
        $this->assertEquals('+33000000000', $array['phone_number']);
    }
}
