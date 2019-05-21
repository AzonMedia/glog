<?php

namespace Azonmedia\Glog\Application;

use Azonmedia\Glog\Middleware\ServingMiddleware;
use Azonmedia\Glog\Storage\StorageProviderFile;
use Azonmedia\Glog\Tasks\FinishHandler;
use Azonmedia\Glog\Tasks\TaskHandler;
use Guzaba2\Base\Base;
use Guzaba2\Http\Body\Stream;
use Guzaba2\Http\StatusCode;
use Guzaba2\Kernel\Kernel;

/**
 * Class Glog
 * This is a configuration file. Has no methods and can not be instantiated
 * @package Azonmedia\Glog\Application
 */
class Glog extends Base
{
    protected const CONFIG_DEFAULTS = [
        'swoole' => [
            'host'       => '0.0.0.0',
            'port'       => 8081,
            'worker_num'        => 4,//http workers
            'task_worker_num'   => 8,//tasks workers
        ],
    ];

    protected static $CONFIG_RUNTIME = [];

    /**
     * @var string
     */
    protected $app_directory;

    public function __construct($app_directory)
    {
        parent::__construct();

        $this->app_directory = $app_directory;

        Kernel::run($this);
    }

    public function __invoke() : int
    {
        return $this->execute();
    }

    public function execute() : int
    {
        $middlewares = [];
//    $middlewares[] = new RoutingMiddleware();
//    $middlewares[] = new FilteringMiddleware();
//    $middlewares[] = new AuthorizationMiddleware();
//    $middlewares[] = new ExecutorMiddleware();


        $HttpServer = new \Guzaba2\Swoole\Server(self::$CONFIG_RUNTIME['swoole']['host'], self::$CONFIG_RUNTIME['swoole']['port'], self::$CONFIG_RUNTIME['swoole']);

        //custom middleware for the app
        $ServingMiddleware = new ServingMiddleware([], $HttpServer);//this serves all requests
        $middlewares[] = $ServingMiddleware;

        $DefaultResponseBody = new Stream();
        $DefaultResponseBody->write('Content not found or request not understood (routing not configured).');
        $DefaultResponse = new \Guzaba2\Http\Response(StatusCode::HTTP_NOT_FOUND, [], $DefaultResponseBody);

        $RequestHandler = new \Guzaba2\Swoole\RequestHandler($middlewares, $HttpServer, $DefaultResponse);

        //https://github.com/swoole/swoole-docs/blob/master/get-started/examples/async_task.md
        $TaskHandler = new TaskHandler(new StorageProviderFile($this->app_directory.'/data/log.txt'));

        //$FinishHandler = new FinishHandler();

        $HttpServer->on('request', $RequestHandler);
        $HttpServer->on('task', $TaskHandler);
        //$HttpServer->on('finish', $FinishHandler);
        $HttpServer->start();

        return Kernel::EXIT_SUCCESS;
    }
}