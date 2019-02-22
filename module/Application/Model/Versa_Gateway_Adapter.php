<?php
namespace Application\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class Versa_Gateway_Adapter
{
	public function init($table)
	{
		$adapter = new Adapter([
			'hostname'		=> '192.168.3.162', // or localhost
			'driver'	=> 'sqlsrv',
			//'dsn'		=> sprintf('sqlite:%s/data/zftutorial.db', realpath(getcwd())),
			'database'	=> 'demo',
			'username'	=> 'sa',
			'password'	=> 'versa12',
		]);
		
		return new TableGateway($table, $adapter);
	}
	
	public function paginator($select, $page = 1, $max_page = 10)
	{
		$adapter = new Adapter([
			'hostname'		=> '192.168.3.162', // or localhost
			'driver'	=> 'sqlsrv',
			//'dsn'		=> sprintf('sqlite:%s/data/zftutorial.db', realpath(getcwd())),
			'database'	=> 'demo',
			'username'	=> 'sa',
			'password'	=> 'versa12',
		]);
		
		$paginator = new Paginator(new DbSelect($select, $adapter, NULL));
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($max_page);
		
		return $paginator;
	}
	
	public function select()
	{
		return new Select();
	}
	
	public function where()
	{
		return new Where();
	}
	
	public function expression($expression)
	{
		return new Expression($expression);
	}
}
