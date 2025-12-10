<?php

namespace Andydefer\AutotextSdk\Enums;

/**
 * Enum agnostique pour le status d'un appareil AutoTextDevice.
 */
enum AutoTextDeviceStatus: string
{
    case ONLINE  = 'online';
    case OFFLINE = 'offline';
    case ERROR   = 'error';
}
