<?php

namespace Core\Builders;

use Core\Response;

class BadRequestBuilder
{
    public static function build(string $message = "Bad Request"): Response
    {
        return new Response($message, 400);
    }
}