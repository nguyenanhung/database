<?php

/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/17/18
 * Time: 00:23
 */
return [
    'development' => [
        'type' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'user' => 'root',
        'pass' => '',
        'database' => 'my_database_development',
        'singleTransaction' => false
    ],
    'production' => [
        'type' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'user' => 'root',
        'pass' => '',
        'database' => 'my_database_production',
        'singleTransaction' => false
    ],
];
