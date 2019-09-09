<?php

namespace Azonmedia\Glog\LogEntries\Views;

use Guzaba2\Mvc\PhpView;

class LogEntries extends PhpView
{
    public function view()
    {
        print '<h1>'.$this->get_structure()['title'].'</h1>';
    }
}