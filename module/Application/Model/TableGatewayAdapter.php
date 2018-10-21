<?php
namespace Application\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class TableGatewayAdapter
{
	public $tableGateway = NULL;
	
	public function __construct()
	{
		$adapter = new Adapter([
			'host'		=> '',
			'driver'	=> 'Pdo',
			'dsn'		=> sprintf('sqlite:%s/data/zftutorial.db', realpath(getcwd())),
			'database'	=> '',
			'username'	=> '',
			'password'	=> '',
		]);
		
		return $this->tableGateway = new TableGateway('demo', $adapter);
	}
}
