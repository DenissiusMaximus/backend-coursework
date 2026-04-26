<?php

namespace Utils\Routing;

use Core\Request;
use Core\Response;

interface IRouteProvider
{
    public function dispatch(Request $request): ?Response;
}
