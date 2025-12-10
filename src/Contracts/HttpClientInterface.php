<?php

namespace Andydefer\AutotextSdk\Contracts;

interface HttpClientInterface
{
    public function post(string $url, array $options): array;
}
