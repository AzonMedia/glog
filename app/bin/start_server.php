<?php
declare(strict_types=1);

namespace Azonmedia\Glog;

use Guzaba2\Kernel\Kernel;
use Guzaba2\Authorization\AuthorizationMiddleware;
use Guzaba2\Authorization\FilteringMiddleware;
use Guzaba2\Mvc\ExecutorMiddleware;
use Guzaba2\Mvc\RoutingMiddleware;
use Azonmedia\Glog\Middleware\ServingMiddleware;

require_once('../../vendor/autoload.php');

const APP_CONFIG = [
    'swoole_host'   => '0.0.0.0',
    'swoole_port'   => 8081,
];


//Kernel::run_swoole('0.0.0.0', 8081);

$bootstrap = function() {

    $app_class_path = realpath('../src/');

    Kernel::register_autoloader_path('Azonmedia\\Glog', $app_class_path);

    $middlewares = [];
//    $middlewares[] = new RoutingMiddleware();
//    $middlewares[] = new FilteringMiddleware();
//    $middlewares[] = new AuthorizationMiddleware();
//    $middlewares[] = new ExecutorMiddleware();



    //custom middleware for the app
    $ServingMiddleware = new ServingMiddleware();//this serves all requests
    $middlewares[] = $ServingMiddleware;

    $RequestHandler = new \Guzaba2\Swoole\RequestHandler($middlewares);
    $HttpServer = new \Guzaba2\Swoole\Server(APP_CONFIG['swoole_host'], APP_CONFIG['swoole_port'], []);
    $HttpServer->on('request', $RequestHandler);
    $HttpServer->start();

    return self::EXIT_SUCCESS;
};

Kernel::run($bootstrap);