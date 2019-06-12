<?php


namespace Azonmedia\Glog\Application;


use Guzaba2\Database\Sql\Mysql\ConnectionCoroutine;

class MysqlConnection extends ConnectionCoroutine
{

    protected const CONFIG_DEFAULTS = [
        'host'      => '192.168.0.92',
        'port'      => 3306,
        'user'      => 'vesko',
        'password'  => 'impas560',
        'database'  => 'guzaba2',
    ];

    protected static $CONFIG_RUNTIME = [];

    public function __construct()
    {
        parent::__construct(self::$CONFIG_RUNTIME);
        print 'NEW CONNECTION'.PHP_EOL;
    }
}