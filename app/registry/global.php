<?php

use Azonmedia\Glog\Application\RedisConnection;
use Guzaba2\Database\ConnectionFactory;
use Guzaba2\Database\ConnectionProviders\Basic;
use Guzaba2\Database\ConnectionProviders\Pool;
use Guzaba2\Orm\MetaStore\NullMetaStore;
use Guzaba2\Orm\MetaStore\SwooleTable;
use Guzaba2\Orm\Store\Memory;
use Guzaba2\Orm\Store\Nosql\Redis;
use Guzaba2\Orm\Store\NullStore;
use Guzaba2\Orm\Store\Sql\Mysql;
use Guzaba2\Kernel\Kernel;

return [
    \Guzaba2\Di\Container::class => [
        'dependencies' => [
            'ConnectionFactory'             => [
                'class'                         => ConnectionFactory::class,
                'args'                          => [
                    'ConnectionProvider'            => 'ConnectionProviderPool',
                    //'ConnectionProvider'            => 'ConnectionProviderBasic',
                ],
            ],
            'ConnectionProviderPool'        => [
                'class'                         => Pool::class,
                'args'                          => [],
            ],
            'ConnectionProviderBasic'       => [
                'class'                         => Basic::class,
                'args'                          => [],
            ],
            'OrmStore'                      => [
                'class'                         => Memory::class,//the Memory store is the first to be looked into
                'args'                          => [
                    'FallbackStore'                 => 'RedisOrmStore',
                ],
            ],
            'RedisOrmStore'                 => [
                'class'                         => Redis::class,
                'args'                          => [
                    'FallbackStore'                 => 'MysqlOrmStore',
                    'connection_class'              => RedisConnection::class,
                ],
            ],
            'RedisCo'                       => [
                'class'                         => RedisConnection::class,
                'args'                          => [
                ],
            ],
            'MysqlOrmStore'                 => [
                'class'                         => Mysql::class,
                'args'                          => [
                    'FallbackStore'                 => 'NullOrmStore',
                    'connection_class'              => \Azonmedia\Glog\Application\MysqlConnection::class,
                ]
            ],
            'NullOrmStore'                  => [
                'class'                         => NullStore::class,
                'args'                          => [
                    'FallbackStore'                 => NULL,
                ],
            ],
            'OrmMetaStore'                  => [
                'class'                         => SwooleTable::class,
                'args'                          => [
                    'FallbackMetaStore'             => 'NullOrmMetaStore',
                ],
                'initialize_immediately'        => TRUE,
            ],
            'NullOrmMetaStore'              => [
                'class'                         => NullMetaStore::class,
                'args'                          => [
                    'FallbackStore'                 => NULL,
                ],
            ],
            'QueryCache' => [
                'class'                         => \Guzaba2\Database\QueryCache::class,
                'args'                          => [
                    // TODO add required params
                ],
            ],
            'LockManager'                   => [
                'class'                         => \Azonmedia\Lock\CoroutineLockManager::class,
                'args'                          => [
                    'Backend'                       => 'LockManagerBackend',
                    'Logger'                        => [Kernel::class, 'get_logger'],
                ],
                'initialize_immediately'        => TRUE,
            ],
            'LockManagerBackend'            => [
                'class'                         => \Azonmedia\Lock\Backends\SwooleTableBackend::class,
                'args'                          => [
                    'Logger'                        => [\Guzaba2\Kernel\Kernel::class, 'get_logger'],
                ],
            ],
        ],
    ]
];