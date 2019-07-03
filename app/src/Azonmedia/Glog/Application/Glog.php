<?php

namespace Azonmedia\Glog\Application;


use Azonmedia\Glog\Home\Controllers\Home;
use Azonmedia\Glog\LogEntries\Controllers\LogEntry;
use Azonmedia\Glog\Middleware\ServingMiddleware;
use Azonmedia\Glog\Storage\StorageProviderFile;
//use Azonmedia\Glog\Tasks\FinishHandler;
use Azonmedia\Glog\Tasks\TaskHandler;
use Azonmedia\Routing\Router;
use Azonmedia\Routing\RoutingMapArray;
use Azonmedia\UrlRewriting\Rewriter;
use Azonmedia\UrlRewriting\RewritingRulesArray;
use Guzaba2\Application\Application;
//use Guzaba2\Base\Base;
use Guzaba2\Database\ConnectionFactory;
use Guzaba2\Database\ConnectionProviders\Pool;
use Guzaba2\Database\ConnectionProviders\Basic;
use Guzaba2\Di\Container;
use Guzaba2\Http\Body\Stream;
use Guzaba2\Http\Method;
use Guzaba2\Http\StatusCode;
use Guzaba2\Kernel\Kernel;
use Guzaba2\Http\RewritingMiddleware;
use Guzaba2\Mvc\ExecutorMiddleware;
use Guzaba2\Mvc\RoutingMiddleware;
use Guzaba2\Swoole\ApplicationMiddleware;
use Guzaba2\Mvc\RestMiddleware;
use Guzaba2\Swoole\WorkerHandler;

class c1
{
    public $p1 = 'aaa';
}

/**
 * Class Glog
 * This is a configuration file. Has no methods and can not be instantiated
 * @package Azonmedia\Glog\Application
 */
class Glog extends Application
{
    protected const CONFIG_DEFAULTS = [
        'swoole' => [ //this array will be passed to $SwooleHttpServer->set()
            'host'              => '0.0.0.0',
            'port'              => 8081,
            'worker_num'        => 6,//http workers
            'task_worker_num'   => 8,//tasks workers
        ],
        'pool' => [
            'max_connections'       => 12, //max for each type
            //connections cant be initialized before the request serving is started (which is inside a coroutine)
            //'connections'           => [
            //    MysqlConnection::class,
            //]
        ]
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

        $DependencyContainer = new Container();
        kernel::set_di_container($DependencyContainer);

        $middlewares = [];
//    $middlewares[] = new RoutingMiddleware();
//    $middlewares[] = new FilteringMiddleware();
//    $middlewares[] = new AuthorizationMiddleware();
//    $middlewares[] = new ExecutorMiddleware();
        //PresenterMiddleware





        $HttpServer = new \Guzaba2\Swoole\Server(self::$CONFIG_RUNTIME['swoole']['host'], self::$CONFIG_RUNTIME['swoole']['port'], self::$CONFIG_RUNTIME['swoole']);

        // TODO disable coroutine for debugging
        // $HttpServer->set(['enable_coroutine' => false,]);

        $ApplicationMiddleware = new ApplicationMiddleware();//blocks static content
        $RestMiddleware = new RestMiddleware();

        $Rewriter = new Rewriter(new RewritingRulesArray([]));
        $RewritingMiddleware = new RewritingMiddleware($HttpServer, $Rewriter);

        $routing_table = [
            '/'             => [
                Method::HTTP_GET        => [Home::class, 'view'],
            ],
            '/log-entry'    => [
                Method::HTTP_GET        => [LogEntry::class, 'view'],
                Method::HTTP_POST       => [LogEntry::class, 'create'],
                Method::HTTP_PUT        => [LogEntry::class, 'update'],
                Method::HTTP_DELETE     => [LogEntry::class, 'delete'],
                //Method::HTTP_HEAD       => [LogEntry::class, 'head'],
                //Method::HTTP_OPTIONS    => [LogEntry::class, 'options'],
            ],
            '/log-entries'  => [],
        ];
        $Router = new Router(new RoutingMapArray($routing_table));
        $RoutingMiddleware = new RoutingMiddleware($HttpServer, $Router);

        //custom middleware for the app
        //$ServingMiddleware = new ServingMiddleware($HttpServer, []);//this serves all requests

        $ExecutorMiddleware = new ExecutorMiddleware($HttpServer);

        $middlewares[] = $RestMiddleware;
        $middlewares[] = $ApplicationMiddleware;
        $middlewares[] = $RewritingMiddleware;
        $middlewares[] = $RoutingMiddleware;
        //$middlewares[] = $ServingMiddleware;//this is a custom middleware
        $middlewares[] = $ExecutorMiddleware;

        $DefaultResponseBody = new Stream();
        $DefaultResponseBody->write('Content not found or request not understood (routing not configured).');
        $DefaultResponse = new \Guzaba2\Http\Response(StatusCode::HTTP_NOT_FOUND, [], $DefaultResponseBody);

        $RequestHandler = new \Guzaba2\Swoole\RequestHandler($middlewares, $HttpServer, $DefaultResponse);

        $WorkerHandler = new WorkerHandler($HttpServer);

        //https://github.com/swoole/swoole-docs/blob/master/get-started/examples/async_task.md
        $TaskHandler = new TaskHandler(new StorageProviderFile($this->app_directory.'/data/log.txt'));

        //$FinishHandler = new FinishHandler();


        //$Pool = new Pool();
        //$Pool->initialize(self::$CONFIG_RUNTIME['pool']);
        //$Pool = new Basic();
        //$ConnectionFactory = ConnectionFactory::get_instance();
        //$ConnectionFactory->set_connection_provider($Pool);
        //$Pool = new Pool(self::$CONFIG_RUNTIME['pool']);
        //$ConnectionFactory =

        //$Services = new Services



        $HttpServer->on('WorkerStart', $WorkerHandler);
        $HttpServer->on('request', $RequestHandler);
        $HttpServer->on('task', $TaskHandler);
        //$HttpServer->on('finish', $FinishHandler);
        $HttpServer->start();

        return Kernel::EXIT_SUCCESS;
    }
}