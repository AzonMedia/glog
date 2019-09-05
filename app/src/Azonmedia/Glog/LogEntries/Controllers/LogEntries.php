<?php

namespace Azonmedia\Glog\LogEntries\Controllers;

use Guzaba2\Mvc\Controller;
use Psr\Http\Message\ResponseInterface;
use Guzaba2\Translator\Translator as t;

class LogEntries extends Controller
{
    public function view() : ResponseInterface
    {

//        $LogEntry = new \Azonmedia\Glog\LogEntries\Models\LogEntry(1);
//        //print $LogEntry->log_entry_content.PHP_EOL;
//        //\Co::sleep(10);
//
//
//        $data = 'ok from '.$this->get_request()->getServer()->get_swoole_server()->worker_id;
//        $Response = parent::get_stream_ok_response($data);
//        return $Response;

        $Response = parent::get_structured_ok_response([]);
        $structure =& $Response->getBody()->getStructure();
        $structure['title'] = t::_('All entries');
        return $Response;
    }
}