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
	
	public function init($table)
	{
		$adapter = new Adapter([
			'host'		=> '',
			'driver'	=> 'Pdo',
			'dsn'		=> sprintf('sqlite:%s/data/zftutorial.db', realpath(getcwd())),
			'database'	=> '',
			'username'	=> '',
			'password'	=> '',
		]);
		
		return new TableGateway($table, $adapter);
	}
	
	public function paginator($table, $page = 1, $max_page = 10)
	{
		$adapter = new Adapter([
			'host'		=> '',
			'driver'	=> 'Pdo',
			'dsn'		=> sprintf('sqlite:%s/data/zftutorial.db', realpath(getcwd())),
			'database'	=> '',
			'username'	=> '',
			'password'	=> '',
		]);
		
		$paginator = new Paginator(new DbSelect(new Select($table), $adapter, NULL));
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($max_page);
		
		return $paginator;
	}
}
