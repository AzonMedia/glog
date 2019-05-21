<?php

namespace Azonmedia\Glog\Storage\Interfaces;

use Azonmedia\Glog\LogEntries\Models\LogEntry;

interface StorageProviderInterface
{

    public function store(LogEntry $LogEntry) : bool ;
}