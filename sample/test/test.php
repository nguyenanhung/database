<?php

/**
 * Project database
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 09/22/2021
 * Time: 01:09
 */
require_once __DIR__ . '/../../vendor/autoload.php';

use nguyenanhung\Bear\Database\Console;
use nguyenanhung\Bear\Database\Support;
use nguyenanhung\MyDatabase\Model\BaseModel;

const DB_HOST = '127.0.0.1';
const DB_PORT = 33060;

$database = [
    'driver' => 'mysql',
    'host' => DB_HOST,
    'port' => DB_PORT,
    'database' => 'all_talentbank_data',
    'username' => 'root',
    'password' => 'hungna',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
];

$model = new BaseModel();
$model->debugStatus = true;
$model->debugLevel = 'info';
$model->debugLoggerPath = __DIR__ . '/../../tmp';
$model->__construct();
$model->setDatabase($database, 'connectionTest');
$model->setTable('job_categories');

//Console::writeLn(date('Y-m-d H:i:s') . ' -> Testing PHP Package Database Base Model Class by HungNG');
//Console::writeLn(date('Y-m-d H:i:s') . ' SDK Version: -> ' . $model->getVersion());
//Console::writeLn(date('Y-m-d H:i:s') . ' SDK Information: -> ' . $model->getSDKPropertiesInfo(true));
//Console::writeLn(date('Y-m-d H:i:s') . ' Default Primary Key: -> ' . $model->getPrimaryKey());
//Console::writeLn(date('Y-m-d H:i:s') . ' Table Info: -> ' . $model->getTable());
//Console::writeLn(date('Y-m-d H:i:s') . ' Database Info -> ' . json_encode($model->getDatabase()));
//Support::phpTelnet(DB_HOST, DB_PORT);
//Support::checkConnectDatabase(DB_HOST, DB_PORT, 'all_talentbank_data', 'root', 'hungna');

$get = $model->getDistinctResultByColumn(
    [
        'job_id',
        'category_id'
    ],
    [
        'job_id' => [
            'field' => 'job_id',
            'operator' => '=',
            'value' => 1
        ]
    ]
);
$result = collect($get)->unique('job_id');
d($result);
