<?php

namespace Azonmedia\Glog\LogEntries\Controllers;

use Azonmedia\Glog\Application\MysqlConnection;
use Azonmedia\Glog\Application\MysqlTest;
use Guzaba2\Coroutine\Coroutine;
use Guzaba2\Database\ConnectionFactory;
use Guzaba2\Database\ConnectionProviders\Basic;
use Guzaba2\Database\ConnectionProviders\Pool;
use Guzaba2\Database\Sql\Mysql\ConnectionCoroutine;
use Guzaba2\Mvc\Controller;
use Guzaba2\Orm\Exceptions\RecordNotFoundException;
use Guzaba2\Translator\Translator as t;
use Psr\Http\Message\ResponseInterface;

class LogEntry extends Controller
{

    //public function view(int $id) : ResponseInterface
    public function view(int $id) : ResponseInterface
    {

        //$this->test1();
        //$this->test3();
        //$LogEntry = new \Azonmedia\Glog\LogEntries\Models\LogEntry(0);
        //$LogEntry->log_entry_content = 'ffggghhhhh';
        //$LogEntry->save();

        //$LogEntry = new \Azonmedia\Glog\LogEntries\Models\LogEntry(1);
        //$LogEntry = new \Azonmedia\Glog\LogEntries\Models\LogEntry(0);
        //$LogEntry->log_entry_content = 'test1';
        //$LogEntry->save();
        //$LogEntry = new \Azonmedia\Glog\LogEntries\Models\LogEntry(100000);
//        $LogEntry = new \Azonmedia\Glog\LogEntries\Models\LogEntry( ['log_entry_content' => 'test1'] );
//        //print $LogEntry->get_index().' '.$LogEntry->log_entry_content;
//
//        //\Co::sleep(15);
//
//        $data = 'ok from '.$this->get_request()->getServer()->get_swoole_server()->worker_id;
//        //$data = 'cnt coroutines: '.count(Coroutine::$coroutines_ids);
//        $Response = parent::get_stream_ok_response($data);
//        //$Response = parent::get_string_ok_response($data);
//        return $Response;


        try {
            $LogEntry = new \Azonmedia\Glog\LogEntries\Models\LogEntry( $id );

            $LogEntry->log_entry_content = 'asasdasd';
            $LogEntry->log_entry_content = 'asasdasd2';
            //print_r($LogEntry->get_property_old_values('log_entry_content'));
            //$LogEntry->get_property_old_values('log_entry_content')[0];
            //print $LogEntry->get_property_old_value('log_entry_content');

            $Response = parent::get_structured_ok_response();
            $structure =& $Response->getBody()->getStructure();
            $structure = array_merge($structure, $LogEntry->get_record_data(), $LogEntry->get_meta_data());
        } catch (RecordNotFoundException $Exception) {
            $Response = parent::get_structured_notfound_response();
            $structure =& $Response->getBody()->getStructure();
            $structure['message'] = sprintf(t::_('There is no log entry with id %s.'), $id);
        }
        return $Response;

        //$data = 'ok';
        //$Response = parent::get_string_ok_response($data);
        //return $Response;

    }

//    public function test1() {
//        $LogEntry = new \Azonmedia\Glog\LogEntries\Models\LogEntry(1);
//        $LogEntry->log_entry_content = 'aaa222';
//        $this->test2();
//        print $LogEntry->log_entry_content.PHP_EOL;
//        $LogEntry->save();
//    }
//
//    public function test2() {
//        $LogEntry = new \Azonmedia\Glog\LogEntries\Models\LogEntry(1);
//        print $LogEntry->log_entry_content.PHP_EOL;
//        $LogEntry->log_entry_content = 'fffgggg';
//    }
//
//    public function test3() {
//        $LogEntry = new \Azonmedia\Glog\LogEntries\Models\LogEntry(1);
//
//        //print 'unmodified: '.$LogEntry->log_entry_content.PHP_EOL;
//        $LogEntry->log_entry_content = 'gggg';
//        //print_r($LogEntry->debug_get_data());
//        //print $LogEntry->is_modified().'aaa';
//        $this->test4();
//    }
//
//    public function test4() {
//        $LogEntry = new \Azonmedia\Glog\LogEntries\Models\LogEntry(1);
//        //print_r($LogEntry->debug_get_data());
//        print $LogEntry->log_entry_content.PHP_EOL;
//        print $LogEntry->is_modified().'bbb';//this will say it is not modified... as this is a different object
//    }

    public function create() : ResponseInterface
    {
        $Response = parent::get_structured_ok_response();
        $struct =& $Response->getBody()->getStructure();
        $struct['message'] = sprintf(t::_('The log entry is accepted.'));

        return $Response;
    }

    public function update(int $id) : ResponseInterface
    {
        $data = sprintf(t::_('log entry %s is updated'), $id);
        $Response = parent::get_string_ok_response($data);
        return $Response;
    }

    public function delete(int $id) : ResponseInterface
    {

    }
}