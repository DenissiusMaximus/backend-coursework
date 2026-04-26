<?php

namespace Core\Pipeline;

use Core\Request;
use Core\Response;

abstract class AbstractHandler implements Handler
{
    private $nextHandler;

    public function setNext(Handler $handler): Handler
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(Request $request): Response
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($request);
        }

        return new Response(); 
    }
}
