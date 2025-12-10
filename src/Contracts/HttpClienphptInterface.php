<?php

namespace Andydefer\AutotextSdk\Services\Contracts;

interface HttpClientInterface
{
    public function post(string $url, array $options): array;
}
