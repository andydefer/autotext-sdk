<?php

// src/Enums/FcmActionType.php

namespace Andydefer\AutotextSdk\Enums;

enum FcmActionType: string
{
    case SEND_SMS = 'send_sms';
    case INFO = 'info';
    case PING = 'ping';
    case CONFIRM_SMS = 'confirm_sms';

    public function label(): string
    {
        return match ($this) {
            self::SEND_SMS => 'Envoyer SMS',
            self::INFO => 'Information',
            self::PING => 'Ping de disponibilité',
            self::CONFIRM_SMS => 'Confirmation SMS',
        };
    }
}
