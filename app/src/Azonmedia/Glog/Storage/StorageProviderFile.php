<?php

namespace Azonmedia\Glog\Storage;

use Azonmedia\Glog\LogEntries\Models\LogEntry;
use Azonmedia\Glog\Storage\Interfaces\StorageProviderInterface;

class StorageProviderFile
implements StorageProviderInterface
{

    /**
     * @var string
     */
    protected $log_path;

    public function __construct(string $log_path)
    {
        $this->log_path = $log_path;
    }

    public function store(LogEntry $LogEntry) : bool
    {
        return (bool) file_put_contents($this->log_path, (string) $LogEntry, \FILE_APPEND);
    }
}