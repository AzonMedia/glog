<?php

namespace Azonmedia\Glog\Tasks;

use Azonmedia\Glog\LogEntries\Models\LogEntry;
use Azonmedia\Glog\Storage\Interfaces\StorageProviderInterface;
use Guzaba2\Base\Base;

class TaskHandler extends Base
{

    /**
     * @var array of StorageProviderInterface
     */
    protected $storage_providers = [];

    public function __construct(StorageProviderInterface $StorageProvider)
    {
        parent::__construct();
        $this->add_provider($StorageProvider);
    }

    /**
     * Adds a new storage provider
     * Returns FALSE if the provider is already added
     * @param StorageProviderInterface $storageProvider
     * @return bool
     */
    public function add_provider(StorageProviderInterface $StorageProvider) : bool
    {
        $ret = FALSE;
        foreach ($this->storage_providers as $RegisteredStorageProvider) {
            if (get_class($RegisteredStorageProvider) === get_class($StorageProvider)) {
                return $ret;
            }
        }
        $this->storage_providers[] = $StorageProvider;
        $ret = TRUE;
        return $ret;
    }

    public function handle(\Swoole\Http\Server $server, int $task_id, int $from_worker_id, /* mixed */ $data) : void
    {
        $LogEntry = new LogEntry($data);
        foreach ($this->storage_providers as $RegisteredStorageProvider) {
            $RegisteredStorageProvider->store($LogEntry);
        }
    }

    public function __invoke(\Swoole\Http\Server $server, int $task_id, int $from_worker_id, /* mixed */ $data) : void
    {
        $this->handle($server, $task_id, $from_worker_id, $data);
    }
}