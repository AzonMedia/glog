<?php

use Guzaba2\Orm\Store\Sql\Mysql;

return [
    \Guzaba2\Di\Container::class => [
        'MysqlOrmStore'                 => [
            'class'                         => Mysql::class,
            'args'                          => [
                'FallbackStore'                 => 'NullOrmStore',
                'connection_class'              => \Azonmedia\Glog\Application\MysqlConnection::class,
            ]
        ],
    ]
];