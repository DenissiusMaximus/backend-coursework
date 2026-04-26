<?php

namespace Core\Builders;

use Core\Request;

class RequestBuilder
{
    public static function build(): Request
    {
        return new Request(
            method: $_SERVER['REQUEST_METHOD'],
            uri: $_SERVER['REQUEST_URI'],
            getParams: $_GET,
            postData: $_POST
        );
    }
}
