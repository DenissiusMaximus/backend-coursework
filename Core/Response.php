<?php

namespace Core;

class Response
{
    public function __construct(
        private string $body = '',
        private int $statusCode = 200,
        private array $headers = []
    ) {
        if (!isset($this->headers['Content-Type'])) {
            $this->headers['Content-Type'] = 'text/html; charset=UTF-8';
        }
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this; 
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public static function redirect(string $url): self
    {
        return new self('', 302, ['Location' => $url]);
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        echo $this->body;
    }
}