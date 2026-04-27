<?php

require_once 'Core/autoload.php';

use Controllers\TransactionController;
use Core\App;
use Middleware\RouterMiddleware;
use Middleware\NotFoundMiddleware;
use Utils\Routing\RouteProvider;

$app = new App();

$routeProvider = new RouteProvider([
	TransactionController::class,
]);

$app->use(new RouterMiddleware($routeProvider));
$app->use(new NotFoundMiddleware());

$app->run();
