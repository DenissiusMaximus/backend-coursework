<?php

require_once 'Core/autoload.php';

use Core\App;
use Utils\Routing\RouteProvider;
use Controllers\AimController;
use Controllers\AuthController;
use Controllers\CategoryController;
use Controllers\HomeController;
use Controllers\SourceController;
use Controllers\TransactionController;
use Controllers\UserController;
use Middleware\AuthMiddleware;
use Middleware\HelperMiddleware;
use Middleware\NotFoundMiddleware;
use Middleware\RouterMiddleware;

$app = new App();

$routeProvider = new RouteProvider([
    HomeController::class,
    TransactionController::class,
    CategoryController::class,
    SourceController::class,
    AimController::class,
    UserController::class,
    AuthController::class,
]);


$app->use(new HelperMiddleware());
$app->use(new AuthMiddleware());
$app->use(new RouterMiddleware($routeProvider));
$app->use(new NotFoundMiddleware());

$app->run();
