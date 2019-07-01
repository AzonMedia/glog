<?php

namespace Azonmedia\Glog\LogEntries\Controllers;

use Azonmedia\Glog\Application\MysqlConnection;
use Guzaba2\Database\ConnectionFactory;
use Guzaba2\Database\ConnectionProviders\Basic;
use Guzaba2\Database\ConnectionProviders\Pool;
use Guzaba2\Database\Sql\Mysql\ConnectionCoroutine;
use Guzaba2\Mvc\Controller;
use Guzaba2\Translator\Translator as t;
use Psr\Http\Message\ResponseInterface;

class LogEntry extends Controller
{

    //public function view(int $id) : ResponseInterface
    public function view() : ResponseInterface
    {

        /*
        $conf = [
            'host'      => '192.168.0.92',
            'port'      => 3306,
            'user'      => 'vesko',
            'password'  => 'impas560',
            'database'  => 'guzaba2',
        ];
        $Connection = new ConnectionCoroutine($conf);
        */
        //$q = "SELECT * FROM some_table";
        //$s = $Connection->prepare($q);

        /*
        $conf = [
            'max_connections'   => 4,
            'connections'       => [
                'connection1' => [
                    'class'     => \Guzaba2\Database\Sql\Mysql\ConnectionCoroutine::class,
                    'settings'  => [
                        'host'      => '192.168.0.92',
                        'port'      => 3306,
                        'user'      => 'vesko',
                        'password'  => 'impas560',
                        'database'  => 'guzaba2',
                    ],
                ],
            ],
        ];
        */
        //print '==================='.PHP_EOL;


        $LogEntry = new \Azonmedia\Glog\LogEntries\Models\LogEntry('');
        $LogEntry->test();


        $Response = parent::get_stream_ok_response('ok');
        return $Response;
    }

    public function create() : ResponseInterface
    {
        $Response = parent::get_structured_ok_response();
        $struct =& $Response->getBody()->getStructure();
        $struct['message'] = sprintf(t::_('The log entry is accepted.'));

        return $Response;
    }

    public function update(int $id) : ResponseInterface
    {

    }

    public function delete(int $id) : ResponseInterface
    {

    }
}