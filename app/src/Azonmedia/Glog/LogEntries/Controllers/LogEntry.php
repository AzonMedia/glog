<?php

namespace Azonmedia\Glog\LogEntries\Controllers;

use Guzaba2\Mvc\Controller;
use Guzaba2\Translator\Translator as t;
use Psr\Http\Message\ResponseInterface;

class LogEntry extends Controller
{

    public function view(int $id) : ResponseInterface
    {

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