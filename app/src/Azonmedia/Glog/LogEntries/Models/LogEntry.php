<?php

namespace Azonmedia\Glog\LogEntries\Models;

use Guzaba2\Base\Base;

class LogEntry extends Base
{

    /**
     * @var string
     */
    protected $entry_data;

    /**
     * When the log entry was created (which is different from when the actual event occurred)
     * @var int
     */
    protected $created_time;

    public function __construct(string $json_data)
    {
        $this->entry_data = json_decode($json_data);
    }

    public function __toString() : string
    {
        return print_r($this->entry_data, TRUE);
    }
}