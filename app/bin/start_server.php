<?php

namespace Azonmedia\Glog;

use Guzaba2\Kernel\Kernel;

require_once('../../vendor/autoload.php');

const APP_CONFIG = [
    'swoole_host'   => '0.0.0.0',
    'swoole_port'   => 8081,
];


//Kernel::run_swoole('0.0.0.0', 8081);

$bootstrap = function() {
    $request_handler = $request_handler ?? new \Guzaba2\Swoole\RequestHandler();
    $http_server = new \Guzaba2\Swoole\Server(APP_CONFIG['swoole_host'], APP_CONFIG['swoole_port'], []);
    $http_server->on('request', $request_handler);
    $http_server->start();

    return self::EXIT_SUCCESS;
};

Kernel::run($bootstrap);