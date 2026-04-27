<?php

namespace Middleware;

use Core\Builders\BadRequestBuilder;
use Core\Pipeline\AbstractMiddleware;
use Core\Request;
use Core\Response;

class NotFoundMiddleware extends AbstractMiddleware
{
    public function handle(Request $request): Response
    {
        $html = "<h1>404 Error</h1>";
        
        return new Response($html, 404);
    }
}
