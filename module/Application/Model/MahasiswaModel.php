<?php
namespace Application\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class MahasiswaModel
{
	function getList()
	{
		$adapter = new Adapter([
			'host'		=> '',
			'driver'	=> 'Pdo',
			'dsn'		=> sprintf('sqlite:%s/data/zftutorial.db', realpath(getcwd())),
			'database'	=> '',
			'username'	=> '',
			'password'	=> '',
		]);
		$tableGateway = new TableGateway('album', $adapter);
		echo '<pre>'; var_dump($tableGateway); exit();
		$result = $tableGateway->select();
		$data = $result->fetchAll();
		foreach ($data as $value) {
			var_dump($value->title);
		}
		exit();
	}
}
