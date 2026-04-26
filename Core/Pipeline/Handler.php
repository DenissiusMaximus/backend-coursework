<?php

namespace Core\Pipeline;

use Core\Request;
use Core\Response;

interface Handler
{
    public function setNext(Handler $handler): Handler;

    public function handle(Request $request): Response;
}
