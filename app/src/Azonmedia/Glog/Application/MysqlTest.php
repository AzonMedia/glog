<?php


namespace Azonmedia\Glog\Application;


class MysqlTest extends MysqlConnection
{
    protected const CONFIG_DEFAULTS = [
        'host'      => '192.168.0.92',
        'port'      => 3306,
        'user'      => 'vesko',
        'password'  => 'impas560',
        'database'  => 'guzaba2',
    ];

    protected const CONFIG_RUNTIME = [];

    public function __construct()
    {

        parent::__construct();

    }
}