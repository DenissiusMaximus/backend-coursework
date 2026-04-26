<?php

namespace Utils\Routing;

use Core\Attributes\Route;
use Core\Request;
use Core\Response;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

class RouteProvider implements IRouteProvider
{
    private ?array $routes = null;

    public function __construct(
        private readonly array $controllers,
    ) {}

    public function dispatch(Request $request): ?Response
    {
        $uri = $this->normalizeUri($request->uri);

        foreach ($this->getRoutes() as $route) {
            if ($route['httpMethod'] !== $request->method) {
                continue;
            }

            if (!preg_match($route['regexPattern'], $uri, $matches)) {
                continue;
            }

            return $this->invokeController($route, $request, $matches);
        }

        return null;
    }

    private function getRoutes(): array
    {
        if ($this->routes !== null) {
            return $this->routes;
        }

        $routes = [];

        foreach ($this->controllers as $controllerClass) {
            $reflection = new ReflectionClass($controllerClass);
            $basePath = $this->extractBasePath($reflection);

            foreach ($reflection->getMethods() as $method) {
                $routeAttribute = $this->extractRouteAttribute($method);
                if ($routeAttribute === null) {
                    continue;
                }

                [$httpMethod, $methodPath] = $this->resolveRouteMeta($routeAttribute);
                $fullPath = $this->combinePaths($basePath, $methodPath);

                $routes[] = [
                    'httpMethod' => $httpMethod,
                    'regexPattern' => $this->buildRegexPattern($fullPath),
                    'controllerClass' => $controllerClass,
                    'method' => $method,
                ];
            }
        }

        $this->routes = $routes;

        return $routes;
    }

    private function extractRouteAttribute(ReflectionMethod $method): ?Route
    {
        $attributes = $method->getAttributes(Route::class);
        if (empty($attributes)) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    private function buildRegexPattern(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);

        return '#^' . $pattern . '$#';
    }

    private function extractBasePath(ReflectionClass $reflection): string
    {
        $attributes = $reflection->getAttributes(Route::class);
        if (empty($attributes)) {
            return '';
        }

        $classRoute = $attributes[0]->newInstance();

        return $this->normalizePath($classRoute->path);
    }

    private function resolveRouteMeta(Route $route): array
    {
        $legacyMethod = strtoupper($route->path);

        if ($this->isHttpMethod($legacyMethod) && $this->looksLikePath($route->method)) {
            return [$legacyMethod, $this->normalizePath($route->method)];
        }

        return [strtoupper($route->method), $this->normalizePath($route->path)];
    }

    private function combinePaths(string $basePath, string $methodPath): string
    {
        if ($basePath === '') {
            return $methodPath;
        }

        if ($methodPath === '/') {
            return $basePath;
        }

        return rtrim($basePath, '/') . '/' . ltrim($methodPath, '/');
    }

    private function normalizePath(string $path): string
    {
        $trimmed = trim($path, '/');

        return $trimmed === '' ? '/' : '/' . $trimmed;
    }

    private function isHttpMethod(string $value): bool
    {
        return in_array($value, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'], true);
    }

    private function looksLikePath(string $value): bool
    {
        return str_starts_with($value, '/');
    }

    private function invokeController(array $route, Request $request, array $matches): Response
    {
        $controller = new $route['controllerClass']();
        $method = $route['method'];

        $args = $this->buildMethodArguments($method, $request, $matches);
        $result = $method->invokeArgs($controller, $args);

        if ($result instanceof Response) {
            return $result;
        }

        if (is_scalar($result)) {
            return new Response((string)$result);
        }

        return new Response();
    }

    private function buildMethodArguments(ReflectionMethod $method, Request $request, array $matches): array
    {
        $jsonData = $request->getJson();
        if (!is_array($jsonData)) {
            $jsonData = [];
        }

        $inputData = array_merge($jsonData, $request->postData);
        $args = [];

        foreach ($method->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            $parameterType = $this->resolveTypeName($parameter);

            if ($parameterType === Request::class) {
                $args[] = $request;
                continue;
            }

            if (array_key_exists($parameterName, $matches)) {
                $args[] = $this->castValue($matches[$parameterName], $parameterType);
                continue;
            }

            if (array_key_exists($parameterName, $inputData)) {
                $args[] = $this->castValue($inputData[$parameterName], $parameterType);
                continue;
            }

            $args[] = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
        }

        return $args;
    }

    private function resolveTypeName(ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();
        if (!$type instanceof ReflectionType) {
            return null;
        }

        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        }

        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $namedType) {
                if ($namedType->getName() === 'null') {
                    continue;
                }

                return $namedType->getName();
            }
        }

        return null;
    }

    private function castValue(mixed $value, ?string $type): mixed
    {
        return match ($type) {
            'int' => (int)$value,
            'float' => (float)str_replace(',', '.', (string)$value),
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'string' => (string)$value,
            default => $value,
        };
    }

    private function normalizeUri(string $rawUri): string
    {
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        $uri = strtok($rawUri, '?');

        if (!empty($basePath) && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = trim($uri, '/');

        return '/' . ltrim($uri, '/');
    }
}
