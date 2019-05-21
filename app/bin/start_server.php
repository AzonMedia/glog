<?php
declare(strict_types=1);

namespace Azonmedia\Glog;

use Azonmedia\Glog\Application\Glog;
use Azonmedia\Registry\Interfaces\RegistryBackendInterface;
use Azonmedia\Registry\Registry;
use Azonmedia\Registry\RegistryBackendEnv;
use Guzaba2\Kernel\Kernel;
use Guzaba2\Authorization\AuthorizationMiddleware;
use Guzaba2\Authorization\FilteringMiddleware;
use Guzaba2\Mvc\ExecutorMiddleware;
use Guzaba2\Mvc\RoutingMiddleware;
use Azonmedia\Glog\Middleware\ServingMiddleware;
use Guzaba2\Registry\Interfaces\RegistryInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require_once('../../vendor/autoload.php');

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

    $initial_directory = getcwd();
    $app_directory = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

    chdir($app_directory);

    //Kernel::initialize(new Registry('AZONMEDIA_GLOG_'), (new Logger('kernellog') )->pushHandler(new StreamHandler('')) );

    $RegistryBackend = new RegistryBackendEnv('');
    $Registry = new Registry($RegistryBackend);


    $Logger = new Logger('main_logger');
    $Logger->pushHandler(new StreamHandler($app_directory.'logs'.DIRECTORY_SEPARATOR.'LOG.txt'));

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


