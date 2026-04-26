<?php

namespace Core;

class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $uri,
        public readonly array $getParams,
        public readonly array $postData
    ) {}

    public function post(string $key, $default = null)
    {
        return $this->postData[$key] ?? $default;
    }

    public function getJson(): ?array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }
}
