<?php

namespace Azonmedia\Glog\Home\Controllers;

use Guzaba2\Mvc\Controller;
use Psr\Http\Message\ResponseInterface;
use Guzaba2\Translator\Translator as t;

class Home extends Controller
{
    public function view() : ResponseInterface
    {
        $Response = parent::get_structured_ok_response();
        $struct =& $Response->getBody()->getStructure();
        $struct['message'] = sprintf(t::_('This is the home page'));

        return $Response;
    }
}