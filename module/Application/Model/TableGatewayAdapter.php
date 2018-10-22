<?php
namespace Application\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

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
	
	public function paginator($table, $page = 1, $max_page = 10)
	{
		$paginator = new Paginator(new DbSelect(new Select($table), $this->tableGateway->getAdapter(), NULL));
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($max_page);
		
		return $paginator;
	}
}
