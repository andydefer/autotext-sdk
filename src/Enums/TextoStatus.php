<?php

namespace Andydefer\AutotextSdk\Enums;

/**
 * Enum agnostique pour le status d'un texto.
 */
enum TextoStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED  = 'failed';
}
