<?php

/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/17/18
 * Time: 00:23
 */
return [
	'local' => [
		'type' => 'Local',
		'root' => testBackupPath()
	],
	's3' => [
		'type' => 'AwsS3',
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
		'version' => 'latest',
		'bucket' => '',
		'root' => '',
	],
	'gcs' => [
		'type' => 'Gcs',
		'key' => '',
		'secret' => '',
		'version' => 'latest',
		'bucket' => '',
		'root' => '',
	],
	'rackspace' => [
		'type' => 'Rackspace',
		'username' => '',
		'password' => '',
		'container' => '',
	],
	'dropbox' => [
		'type' => 'Dropbox',
		'token' => '',
		'key' => '',
		'secret' => '',
		'app' => '',
		'root' => '',
	],
	'ftp' => [
		'type' => 'Ftp',
		'host' => '',
		'username' => '',
		'password' => '',
		'root' => '',
		'port' => 21,
		'passive' => true,
		'ssl' => true,
		'timeout' => 30,
	],
	'sftp' => [
		'type' => 'Sftp',
		'host' => '',
		'username' => '',
		'password' => '',
		'root' => '',
		'port' => 21,
		'timeout' => 10,
		'privateKey' => '',
	],
];
