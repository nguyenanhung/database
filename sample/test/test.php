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

$database = [
	'driver' => 'mysql',
	'host' => 'localhost',
	'database' => 'test_database',
	'username' => 'root',
	'password' => '',
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
$model->setTable('test_table');

Console::writeLn(date('Y-m-d H:i:s') . ' -> Testing PHP Package Database Base Model Class by HungNG');
Console::writeLn(date('Y-m-d H:i:s') . ' SDK Version: -> ' . $model->getVersion());
Console::writeLn(date('Y-m-d H:i:s') . ' SDK Infomation: -> ' . $model->getSDKPropertiesInfo(true));
Console::writeLn(date('Y-m-d H:i:s') . ' Default Primary Key: -> ' . $model->getPrimaryKey());
Console::writeLn(date('Y-m-d H:i:s') . ' Table Info: -> ' . $model->getTable());
Console::writeLn(date('Y-m-d H:i:s') . ' Database Info -> ' . json_encode($model->getDatabase()));
Support::phpTelnet('localhost', 3306);
Support::checkConnectDatabase('127.0.0.1', 3306, 'test_database', 'root');
