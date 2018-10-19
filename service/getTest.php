<?php
include_once "Startup.php";

use Zend\Db\Adapter\Adapter;

$adapter = new Adapter([
	'host'		=> '',
	'driver'	=> 'Pdo',
	'dsn'		=> sprintf('sqlite:%szftutorial.db', realpath(getcwd())),
	'database'	=> '',
	'username'	=> '',
	'password'	=> '',
]);

var_dump($adapter);
