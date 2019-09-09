<?php

namespace Azonmedia\Glog\LogEntries\Views;

use Guzaba2\Mvc\PhpView;

class LogEntry extends PhpView
{
    public function view()
    {
        $structure = $this->get_structure();
        print '<h1>Showing data for log entry #'.$structure['log_entry_id'].'</h1>';
        print '<p>'.$structure['log_entry_content'].'</p>';
    }

    public function create()
    {

    }
}