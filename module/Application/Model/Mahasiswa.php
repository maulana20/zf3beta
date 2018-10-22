<?php
namespace Application\Model;
use Application\Model\TableGatewayAdapter;

use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class Mahasiswa extends TableGatewayAdapter
{
	function getList($page, $max_page)
	{
		$this->tableGateway->getSql()->setTable('posts');
		$paginator = new Paginator(new DbSelect(new Select('posts'), $this->tableGateway->getAdapter(), NULL));
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($max_page);
		
		return $paginator;
	}
}
