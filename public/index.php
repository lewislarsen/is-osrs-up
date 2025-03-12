<?php

use DI\Bridge\Slim\Bridge;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../src/bootstrap.php';

// Create app instance
$container = require __DIR__ . '/../src/config.php';
$app = Bridge::create($container);

$app->get('/', 'App\Controllers\HomeController:index');
$app->get('/api/status', 'App\Controllers\ApiController:getStatus');
$app->get('/api/history', 'App\Controllers\ApiController:getHistory');

$app->run();