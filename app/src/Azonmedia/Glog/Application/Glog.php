<?php

namespace Azonmedia\Glog\Application;


use Azonmedia\Glog\Home\Controllers\Home;
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
use Guzaba2\Swoole\ApplicationMiddleware;
use Guzaba2\Mvc\RestMiddleware;
//use Guzaba2\Swoole\WorkerHandler;
use Guzaba2\Swoole\Handlers\WorkerStart;

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
            'port'              => 8082,
            'worker_num'        => 24,//http workers
            //Swoole\Coroutine::create(): Unable to use async-io in task processes, please set `task_enable_coroutine` to true.
            //'task_worker_num'   => 8,//tasks workers
            'task_worker_num'   => 0,//tasks workers
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
        kernel::set_di_container($DependencyContainer);
        
        $Watchdog = new \Azonmedia\Watchdog\Watchdog();
        kernel::set_watchdog($Watchdog);
        
        $middlewares = [];
//    $middlewares[] = new RoutingMiddleware();
//    $middlewares[] = new FilteringMiddleware();
//    $middlewares[] = new AuthorizationMiddleware();
//    $middlewares[] = new ExecutorMiddleware();
        //PresenterMiddleware




        $HttpServer = new \Guzaba2\Swoole\Server(self::CONFIG_RUNTIME['swoole']['host'], self::CONFIG_RUNTIME['swoole']['port'], self::CONFIG_RUNTIME['swoole']);

        // disable coroutine for debugging
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
        $GlogMiddleware = new GlogMiddleware($this, $HttpServer);

        $ExecutorMiddleware = new ExecutorMiddleware($HttpServer);

        $middlewares[] = $RestMiddleware;
        $middlewares[] = $ApplicationMiddleware;
        $middlewares[] = $RewritingMiddleware;
        $middlewares[] = $RoutingMiddleware;
        //$middlewares[] = $ServingMiddleware;//this is a custom middleware
        $middlewares[] = $GlogMiddleware;//custom middlware used by this app
        $middlewares[] = $ExecutorMiddleware;

        $DefaultResponseBody = new Stream();
        $DefaultResponseBody->write('Content not found or request not understood (routing not configured).');
        //$DefaultResponseBody = new \Guzaba2\Http\Body\Str();
        //$DefaultResponseBody->write('Content not found or request not understood (routing not configured).');
        $DefaultResponse = new \Guzaba2\Http\Response(StatusCode::HTTP_NOT_FOUND, [], $DefaultResponseBody);

        //$RequestHandler = new \Guzaba2\Swoole\RequestHandler($middlewares, $HttpServer, $DefaultResponse);
        $RequestHandler = new \Guzaba2\Swoole\Handlers\Http\Request($HttpServer, $middlewares, $DefaultResponse);

        //$WorkerHandler = new WorkerHandler($HttpServer);
        $WorkerHandler = new WorkerStart($HttpServer);


        //https://github.com/swoole/swoole-docs/blob/master/get-started/examples/async_task.md
        //$TaskHandler = new TaskHandler(new StorageProviderFile($this->app_directory.'/data/log.txt'));
        //$TaskHandler = new TaskHandler(new StorageProviderFile($this->app_directory.'/data/log.txt'));

        //$FinishHandler = new FinishHandler();


        //$Pool = new Pool();
        //$Pool->initialize(self::CONFIG_RUNTIME['pool']);
        //$Pool = new Basic();
        //$ConnectionFactory = ConnectionFactory::get_instance();
        //$ConnectionFactory->set_connection_provider($Pool);
        //$Pool = new Pool(self::CONFIG_RUNTIME['pool']);
        //$ConnectionFactory =

        //$Services = new Services



        $HttpServer->on('WorkerStart', $WorkerHandler);
        $HttpServer->on('Request', $RequestHandler);


        //$HttpServer->on('Task', $TaskHandler);
        //$HttpServer->on('finish', $FinishHandler);

//        $table = new \Swoole\Table(100);
//        $table->column('id', \Swoole\Table::TYPE_INT);
//        $table->column('data', \Swoole\Table::TYPE_STRING, 128);
//        $table->create();

        //$HttpServer->table = $table;

        $HttpServer->start();
        
                 \Guzaba2\Kernel\Kernel::logtofile('kurvii', array('aide we'));
        
        return Kernel::EXIT_SUCCESS;
    }
}