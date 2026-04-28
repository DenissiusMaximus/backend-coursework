<?php

namespace Utils\Routing;

use ReflectionMethod;

class RouteDefinition
{
    public function __construct(
        public readonly string $httpMethod,
        public readonly string $regexPattern,
        public readonly string $controllerClass,
        public readonly ReflectionMethod $method,
    ) {}
}
