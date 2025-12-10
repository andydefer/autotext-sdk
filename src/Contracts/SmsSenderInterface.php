<?php

namespace Andydefer\AutotextSdk\Contracts;

interface SmsSenderInterface
{
    public function send(string $phone, string $message): string;
}
