<?php
declare(strict_types=1);

namespace Azonmedia\Glog;

use Azonmedia\Di\Container;
use Azonmedia\Glog\Application\Glog;
use Azonmedia\Registry\Interfaces\RegistryBackendInterface;
use Azonmedia\Registry\Registry;
use Azonmedia\Registry\RegistryBackendArray;
use Azonmedia\Registry\RegistryBackendEnv;
use Guzaba2\Database\ConnectionFactory;
use Guzaba2\Database\ConnectionProviders\Pool;
use Guzaba2\Kernel\Kernel;
use Guzaba2\Authorization\AuthorizationMiddleware;
use Guzaba2\Authorization\FilteringMiddleware;
use Guzaba2\Mvc\ExecutorMiddleware;
use Guzaba2\Routing\RoutingMiddleware;
use Azonmedia\Glog\Middleware\ServingMiddleware;
use Guzaba2\Registry\Interfaces\RegistryInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;

$autoload_path = realpath(__DIR__ . '/../../vendor/autoload.php');
require_once($autoload_path);

//https://github.com/swoole/swoole-docs/blob/master/get-started/examples/async_task.md
//https://www.swoole.co.uk/docs/modules/swoole-server/configuration
//these are default values
//but will be overriden by the env vars if they exist
//the env vars have GLOG_ prefix and are all caps
//GLOG_SWOOLE_HOST
/*
const APP_CONFIG = [
    'swoole_host'       => '0.0.0.0',
    'swoole_port'       => 8081,
    'worker_num'        => 4,//http workers
    'task_worker_num'   => 8,//tasks workers
    'data_dir'          => './data/',
    'log_dir'           => './logs/',
];
*/


(function(){

    //ini_set("swoole.enable_preemptive_scheduler","1");
    //\Swoole\Coroutine::set([ 'enable_preemptive_scheduler' => 1 ]);
    //the above is available in Master branch only not released yet

    $log_level = LogLevel::DEBUG;

    $initial_directory = getcwd();
    $app_directory = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

    chdir($app_directory);

    $RegistryBackend = new RegistryBackendArray(realpath(__DIR__ . '/../registry'));
    $Registry = new Registry($RegistryBackend);

    $Logger = new Logger('main_logger');
    $Formatter = new LineFormatter(
        NULL, // Format of message in log, default [%datetime%] %channel%.%level_name%: %message% %context% %extra%\n
        NULL, // Datetime format
        TRUE, // allowInlineLineBreaks option, default false
        TRUE  // ignoreEmptyContextAndExtra option, default false
    );

    $FileHandler = new StreamHandler($app_directory.'logs'.DIRECTORY_SEPARATOR.'LOG.txt', $log_level);
    $FileHandler->setFormatter($Formatter);
    $Logger->pushHandler($FileHandler);


    $StdoutHandler = new StreamHandler('php://stdout', $log_level);
    $StdoutHandler->setFormatter($Formatter);
    $Logger->pushHandler($StdoutHandler);


    Kernel::initialize($Registry, $Logger);

    //from this point the kernel (and most importantly the autoloader) is usable
    //up until this point no Guzaba2 classes can be autoloaded (only composer autoload works - from other packages)

    $app_class_path = realpath($app_directory.'src'.DIRECTORY_SEPARATOR);

    //registers where this application classes are located
    Kernel::register_autoloader_path('Azonmedia\\Glog', $app_class_path);

    //past this point it is possible to autoload Application specific classes

    new Glog($app_directory);

    chdir($initial_directory);
})();


