<?php

namespace Azonmedia\Glog\LogEntries\Models;

use Azonmedia\Glog\Application\MysqlConnection;
use Guzaba2\Base\Base;
use Guzaba2\Coroutine\Coroutine;
use Guzaba2\Database\ConnectionFactory;
use Guzaba2\Orm\ActiveRecord;

class LogEntry extends ActiveRecord
{

    protected const CONFIG_DEFAULTS = [
        'services'      => [
            'ConnectionFactory'
        ],
        'main_table'    => 'log_entries',
    ];

    protected const CONFIG_RUNTIME = [];

    /**
     * @var string
     */
    //protected $entry_data;

    /**
     * When the log entry was accepted for logging (which is different from when the actual event occurred which is provided in the json_data)
     * @var int
     */
    //protected $accepted_microtime;

    //public function __construct(string $json_data)
    public function __construct(int $index)
    {
        parent::__construct($index);

        //print_r(self::CONFIG_RUNTIME);
        //print_r(get_declared_classes());

//        $entry_data = json_decode($json_data);
//        //$this->entry_data = $entry_data ?? ['message' => 'parsing json failed'];
//        $this->entry_data = $entry_data ?? ['message' => $json_data];//if the decoding failed just put as message the provided string
//        $this->accepted_microtime = microtime(TRUE);
    }

    public function __toString() : string
    {
        return print_r($this->entry_data, TRUE);
    }

    public function get_data() : array
    {
        return $this->entry_data;
    }

    public function get_accepted_microtime() : float
    {
        return $this->accepted_microtime;
    }

    public function test() : void
    {
        //$ConnectionFactory = ConnectionFactory::get_instance();
        //$Connection1 = $ConnectionFactory->get_connection(\Guzaba2\Database\Sql\Mysql\ConnectionCoroutine::class);
        $Connection1 = self::ConnectionFactory()->get_connection(MysqlConnection::class);
        //print_r(Coroutine::getContext()->getConnections());

        $query = "SELECT * FROM some_table";
        //\Co::sleep(3);
        //$query = "SELECT SLEEP(1)";
        $Statement = $Connection1->prepare($query);
        $Statement->execute();
        $data = $Statement->fetchAll();
        //print_r($data);

        //print_r(self::CONFIG_RUNTIME);

        //$ConnectionFactory->free_connection($Connection1);
        //self::ConnectionFactory()->free_connection($Connection1);
        //$Connection1->free();//even if not explicitly freed up the coroutine will free it at the end of its execution

    }
}