<?php

namespace Azonmedia\Glog\LogEntries\Models;

use Azonmedia\Glog\Application\MysqlConnection;
use Azonmedia\Lock\Interfaces\LockInterface;
use Guzaba2\Base\Base;
use Guzaba2\Base\Exceptions\RunTimeException;
use Guzaba2\Coroutine\Coroutine;
use Guzaba2\Database\ConnectionFactory;
use Guzaba2\Orm\ActiveRecord;

class LogEntry extends ActiveRecord
{

    protected const CONFIG_DEFAULTS = [
        'services'      => [
            'ConnectionFactory'
        ],
        'main_table'    => 'log_entries',//defines the storage key
        'default_route' => '/log-entry',
    ];

    protected const CONFIG_RUNTIME = [];

    public function test_con() {
        //$Connection = self::ConnectionFactory()->get_connection(MysqlConnection::class, $CR);
        $queries = [ ['query' => 'SELECT 1', 'params' => []], ['query' => 'SELECT 2', 'params' => []] ];
        $ret = MysqlConnection::execute_parallel_queries($queries);
        print_r($ret);
    }

    public function test_con_2() {
//        for ($aa = 0; $aa < 4; $aa++) {
//            $Connection = self::ConnectionFactory()->get_connection(MysqlConnection::class, $CR);
//            print $Connection->get_object_internal_id().PHP_EOL;
//        }

        $Connection = self::ConnectionFactory()->get_connection(MysqlConnection::class, $CR1);
        $this->test_con_3();

        print $Connection->get_coroutine_id().PHP_EOL;
        print 'query result: '.print_r($Connection->prepare('SELECT 1')->execute()->fetchAll(), TRUE).PHP_EOL;

    }

    public function test_con_3() {
        $Connection = self::ConnectionFactory()->get_connection(MysqlConnection::class, $CR1);
        //$Connection->free();
        //$Connection->force_release();
    }
    
    public function _before_save()
    {
       
    }
    
    public function _after_save()
    {
       
    }

    public function test() : void
    {

//        $DebuggerBackend = new \Guzaba2\Swoole\Debug\Backends\Basic();
//        $Debugger = new \Azonmedia\Debug\Debugger($DebuggerBackend);
//        //$this->SwooleDebugger = new \Guzaba2\Swoole\Debug\Debugger($this->HttpServer, $worker_id, $Debugger);
//        $this->SwooleDebugger = new \Guzaba2\Swoole\Debug\Debugger(NULL, 0, $Debugger);

//        //$ConnectionFactory = ConnectionFactory::get_instance();
//        //$Connection1 = $ConnectionFactory->get_connection(\Guzaba2\Database\Sql\Mysql\ConnectionCoroutine::class);
//        $Connection1 = self::ConnectionFactory()->get_connection(MysqlConnection::class);
//        //print_r(Coroutine::getContext()->getConnections());
//
//        $query = "SELECT * FROM some_table";
//        //\Swoole\Coroutine\System::sleep(3);
//        //$query = "SELECT SLEEP(1)";
//        $Statement = $Connection1->prepare($query);
//        $Statement->execute();
//        $data = $Statement->fetchAll();
//        //print_r($data);
//
//        //print_r(self::CONFIG_RUNTIME);
//
//        //$ConnectionFactory->free_connection($Connection1);
//        //self::ConnectionFactory()->free_connection($Connection1);
//        //$Connection1->free();//even if not explicitly freed up the coroutine will free it at the end of its execution


        //$query = "SELECT * FROM some_table";

//        $Connection1 = self::ConnectionFactory()->get_connection(MysqlConnection::class, $CR1);
//
//        $F = function(){
//
//            print 'co started'.PHP_EOL;
//
//            //throw new RunTimeException('eeexxx');
//            //print_r(Coroutine::getFullBacktrace(NULL, \DEBUG_BACKTRACE_IGNORE_ARGS));
//            //TestObj::m2();
//
//            $F2 = function() {
//                //throw new RunTimeException('eeexxx');
//                throw new \Exception('rrrr');
//            };
//            $F2();
//
//            print 'co end'.PHP_EOL;
//        };


        //\Swoole\Coroutine::create($F);
        //\Swoole\Coroutine::create($F);
        //Coroutine::create($F);
        //\Swoole\Coroutine::create($F);


        //\Swoole\Coroutine\System::sleep(1);

        //print 'After coroutine'.PHP_EOL;
        //Swoole\Coroutine\System::sleep(1);
        //Coroutine::create($F);


        //throw new \Guzaba2\Base\Exceptions\RunTimeException('ggggg');

//        $Connection1 = self::ConnectionFactory()->get_connection(MysqlConnection::class, $CR1);
//
//        $queries = [
//            [
//                'query'     => "SELECT SLEEP(5)",
//                'params'    => [],
//            ],
//            [
//                'query'     => "SELECT SLEEP(5)",
//                'params'    => [],
//            ],
//            [
//                'query'     => "SELECT SLEEP(5)",
//                'params'    => [],
//            ],
//        ];
//        $Connection1->execute_multiple_queries($queries);

    }

    public function test55($Request)
    {
        print $Request->getServer()->get_worker_id().' start: '.microtime(true).PHP_EOL;

        //self::LockManager()->acquire_lock('aa', LockInterface::LOCK_PW);
        //self::LockManager()->acquire_lock('aa', LockInterface::LOCK_PW);
        self::LockManager()->acquire_lock($this, LockInterface::LOCK_PW);
        print $Request->getServer()->get_worker_id().' lock obtained: '.microtime(true).PHP_EOL;
        //\Swoole\Coroutine\System::sleep(3);


        print $Request->getServer()->get_worker_id().' end: '.microtime(true).PHP_EOL;

        print_r(self::LockManager()->get_all_own_locks());
        self::LockManager()->release_lock($this);
        print_r(self::LockManager()->get_all_own_locks());

        print self::LockManager()->get_lock_level($this).PHP_EOL;

    }
}