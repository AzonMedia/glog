<?php

namespace Azonmedia\Glog\LogEntries\Models;

use Azonmedia\Glog\Application\MysqlConnection;
use Azonmedia\Lock\Interfaces\LockInterface;
use Guzaba2\Base\Base;
use Guzaba2\Base\Exceptions\RunTimeException;
use Guzaba2\Coroutine\Coroutine;
use Guzaba2\Database\ConnectionFactory;
use Guzaba2\Orm\ActiveRecord;

class ExampleModel extends ActiveRecord
{

    protected const CONFIG_DEFAULTS = [
        'services'      => [
            'ConnectionFactory'
        ],
        'main_table'    => 'example',//defines the storage key
        'structure' => [
            [
                'name' => 'id',
                'native_type' => 'int',
                'php_type' => 'integer',
                'size' => 10,
                'nullable' => false,
                'column_id' => 1,
                'primary' => true,
                'default_value' => 0,
                'autoincrement' => '',
            ],
            [
                'name' => 'name',
                'native_type' => 'varchar',
                'php_type' => 'string',
                'size' => 255,
                'nullable' => false,
                'column_id' => 2,
                'primary' => false,
                'default_value' => '',
            ]
        ]
    ];

    protected const CONFIG_RUNTIME = [];

}