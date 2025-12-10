<?php

namespace Andydefer\AutotextSdk\Contracts;

use Andydefer\AutotextSdk\Dtos\HttpResponseDto;

interface HttpClientInterface
{
    public function post(string $url, array $options): HttpResponseDto;
}
