<?php

namespace Andydefer\AutotextSdk\Dtos;

class HttpResponseDto
{
    public function __construct(
        public int $statusCode,
        public ?array $data,
        public ?string $error = null,
    ) {}


    public function isSuccess(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
}
