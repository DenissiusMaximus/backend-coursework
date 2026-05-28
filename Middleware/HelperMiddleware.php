<?php

namespace Middleware;

use Core\Pipeline\AbstractMiddleware;
use Core\Request;
use Core\Response;
use Utils\Routing\IRouteProvider;

class HelperMiddleware extends AbstractMiddleware
{
    public function __construct() {}

    public function handle(Request $request): Response
    {
        require_once 'Core/helpers.php';

        return parent::handle($request);
    }
}
