<?php

namespace Azonmedia\Glog\Application;

use Azonmedia\Glog\Middleware\ServingMiddleware;
use Guzaba2\Base\Base;
use Guzaba2\Kernel\Kernel;

/**
 * Class Glog
 * This is a configuration file. Has no methods and can not be instantiated
 * @package Azonmedia\Glog\Application
 */
class Glog extends Base
{
    protected const CONFIG_DEFAULTS = [
        'swoole_host'       => '0.0.0.0',
        'swoole_port'       => 8081,
        'worker_num'        => 4,//http workers
        'task_worker_num'   => 8,//tasks workers
    ];

    protected static $CONFIG_RUNTIME = [];

    public function __construct()
    {
        parent::__construct();
        Kernel::run($this);
    }

    public function __invoke() : int
    {

        $middlewares = [];
//    $middlewares[] = new RoutingMiddleware();
//    $middlewares[] = new FilteringMiddleware();
//    $middlewares[] = new AuthorizationMiddleware();
//    $middlewares[] = new ExecutorMiddleware();



        //custom middleware for the app
        $ServingMiddleware = new ServingMiddleware();//this serves all requests
        $middlewares[] = $ServingMiddleware;

        $RequestHandler = new \Guzaba2\Swoole\RequestHandler($middlewares);
        $HttpServer = new \Guzaba2\Swoole\Server(self::$CONFIG_RUNTIME['swoole_host'], self::$CONFIG_RUNTIME['swoole_port'], []);
        $HttpServer->on('request', $RequestHandler);
        $HttpServer->start();

        return Kernel::EXIT_SUCCESS;
    }
}