<?php

namespace Azonmedia\Glog\Application;


use Azonmedia\Glog\Home\Controllers\Home;
use Azonmedia\Glog\LogEntries\Controllers\LogEntries;
use Azonmedia\Glog\LogEntries\Controllers\LogEntry;
//use Azonmedia\Glog\Middleware\ServingMiddleware;
use Azonmedia\Glog\Storage\StorageProviderFile;
//use Azonmedia\Glog\Tasks\FinishHandler;
use Azonmedia\Glog\Tasks\TaskHandler;
use Azonmedia\Routing\Router;
use Azonmedia\Routing\RoutingMapArray;
use Azonmedia\UrlRewriting\Rewriter;
use Azonmedia\UrlRewriting\RewritingRulesArray;
use Guzaba2\Application\Application;
//use Guzaba2\Base\Base;
//use Guzaba2\Database\ConnectionFactory;
//use Guzaba2\Database\ConnectionProviders\Pool;
//use Guzaba2\Database\ConnectionProviders\Basic;
use Guzaba2\Di\Container;
use Guzaba2\Http\Body\Stream;
use Guzaba2\Http\Method;
use Guzaba2\Http\StatusCode;
use Guzaba2\Kernel\Kernel;
use Guzaba2\Http\RewritingMiddleware;
use Guzaba2\Mvc\ExecutorMiddleware;
use Guzaba2\Mvc\RoutingMiddleware;
use Guzaba2\Orm\ActiveRecord;
use Guzaba2\Swoole\ApplicationMiddleware;
use Guzaba2\Mvc\RestMiddleware;
//use Guzaba2\Swoole\WorkerHandler;
use Guzaba2\Swoole\Handlers\WorkerConnect;
use Guzaba2\Swoole\Handlers\WorkerStart;
use Guzaba2\Authorization\IpFilter;

/**
 * Class Glog
 * @package Azonmedia\Glog\Application
 */
class Glog extends Application
{
    protected const CONFIG_DEFAULTS = [
        'swoole' => [ 
            'host'              => '0.0.0.0',
            'port'              => 8081,
            'server_options'    => [ //this array will be passed to $SwooleHttpServer->set()
                'worker_num'        => 4,//http workers
                //Swoole\Coroutine::create(): Unable to use async-io in task processes, please set `task_enable_coroutine` to true.
                //'task_worker_num'   => 8,//tasks workers
                'task_worker_num'   => 0,//tasks workers
            ],
        ],
    ];

    protected const CONFIG_RUNTIME = [];

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
        Kernel::set_di_container($DependencyContainer);
        
        $Watchdog = new \Azonmedia\Watchdog\Watchdog(new \Azonmedia\Watchdog\Backends\SwooleTableBackend());
        Kernel::set_watchdog($Watchdog);
        

        $middlewares = [];
        // $middlewares[] = new RoutingMiddleware();
        // $middlewares[] = new FilteringMiddleware();
        // $middlewares[] = new AuthorizationMiddleware();
        // $middlewares[] = new ExecutorMiddleware();
        //PresenterMiddleware

        $HttpServer = new \Guzaba2\Swoole\Server(self::CONFIG_RUNTIME['swoole']['host'], self::CONFIG_RUNTIME['swoole']['port'], self::CONFIG_RUNTIME['swoole']['server_options']);

        Kernel::set_http_server($HttpServer);


        // disable coroutine for debugging
        // $HttpServer->set(['enable_coroutine' => false,]);
        // $HttpServer->set(['enable_coroutine' => false,]);

        $ApplicationMiddleware = new ApplicationMiddleware();//blocks static content
        $RestMiddleware = new RestMiddleware();

        $Rewriter = new Rewriter(new RewritingRulesArray([]));
        $RewritingMiddleware = new RewritingMiddleware($HttpServer, $Rewriter);

        /*
        $routing_table = [
            '/'                                     => [
                Method::HTTP_GET                        => [Home::class, 'view'],
            ],
            '/log-entry'                            => [
                Method::HTTP_POST                       => [LogEntry::class, 'create'],
            ],
            '/log-entry/{id}'                       => [
                Method::HTTP_GET                        => [LogEntry::class, 'view'],
                Method::HTTP_PUT | Method::HTTP_PATCH   => [LogEntry::class, 'update'],
                Method::HTTP_DELETE                     => [LogEntry::class, 'delete'],
            ],
            '/log-entries'                          => [
                Method::HTTP_GET                        => [LogEntries::class, 'view'],
            ],
        ];
        */
        $routing_table = [
            '/'                                     => [
                Method::HTTP_GET                        => [Home::class, 'view'],
            ],
            '/log-entries'                          => [
                Method::HTTP_GET                        => [LogEntries::class, 'view'],
            ],
        ];
        $default_routes = ActiveRecord::get_default_routes(array_keys(Kernel::get_registered_autoloader_paths()));
        //die(print_r($default_routes));
        //die('stop');
        $routing_table = Router::merge_routes($routing_table, $default_routes);
        //die(print_r($routing_table));

        $Router = new Router(new RoutingMapArray($routing_table));
        $RoutingMiddleware = new RoutingMiddleware($HttpServer, $Router);

        //custom middleware for the app
        //$ServingMiddleware = new ServingMiddleware($HttpServer, []);//this serves all requests
        $GlogMiddleware = new GlogMiddleware($this, $HttpServer);

        $ExecutorMiddleware = new ExecutorMiddleware($HttpServer);

        //adding middlewares slows down significantly the processing
        //$middlewares[] = $RestMiddleware;
        //$middlewares[] = $ApplicationMiddleware;
        //$middlewares[] = $RewritingMiddleware;
        $middlewares[] = $RoutingMiddleware;
        //$middlewares[] = $ServingMiddleware;//this is a custom middleware
        $middlewares[] = $GlogMiddleware;//custom middleware used by this app - disables locking on ActiveRecord on read (get) requests
        $middlewares[] = $ExecutorMiddleware;

        $DefaultResponseBody = new Stream();
        $DefaultResponseBody->write('Content not found or request not understood (routing not configured).');
        //$DefaultResponseBody = new \Guzaba2\Http\Body\Str();
        //$DefaultResponseBody->write('Content not found or request not understood (routing not configured).');
        $DefaultResponse = new \Guzaba2\Http\Response(StatusCode::HTTP_NOT_FOUND, [], $DefaultResponseBody);

        //$RequestHandler = new \Guzaba2\Swoole\RequestHandler($middlewares, $HttpServer, $DefaultResponse);
        $RequestHandler = new \Guzaba2\Swoole\Handlers\Http\Request($HttpServer, $middlewares, $DefaultResponse);

        $ConnectHandler = new WorkerConnect($HttpServer, new IpFilter());
        //$WorkerHandler = new WorkerHandler($HttpServer);
        $WorkerHandler = new WorkerStart($HttpServer);


        $HttpServer->on('Connect', $ConnectHandler);
        $HttpServer->on('WorkerStart', $WorkerHandler);
        $HttpServer->on('Request', $RequestHandler);


        $HttpServer->start();
        
        return Kernel::EXIT_SUCCESS;
    }
}
