<?php

namespace Middleware;

use Core\Pipeline\AbstractHandler;
use Core\Request;
use Core\Response;
use Utils\Routing\IRouteProvider;

class RouterMiddleware extends AbstractHandler
{
    public function __construct(
        private readonly IRouteProvider $routeProvider,
    ) {}

    public function handle(Request $request): Response
    {
        $response = $this->routeProvider->dispatch($request);

        if ($response instanceof Response) {
            return $response;
        }

        return parent::handle($request);
    }
}
