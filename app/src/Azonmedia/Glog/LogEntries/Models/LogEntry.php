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
     * When the log entry was accepted for logging (which is different from when the actual event occurred which is provided in the json_data)
     * @var int
     */
    protected $accepted_microtime;

    public function __construct(string $json_data)
    {
        $this->entry_data = json_decode($json_data);
        $this->accepted_microtime = microtime(TRUE);
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
}