<?php

namespace Azonmedia\Glog\Home\Views;

use Guzaba2\Mvc\PhpView;
use Psr\Http\Message\ResponseInterface;

class Home extends PhpView
{
    public function view() : void
    {
        $structure = $this->Response->getBody()->getStructure();//no reference as it is only for reading
        ?>
<h1><?=$structure['message']?></h1>
        <?php
    }
}