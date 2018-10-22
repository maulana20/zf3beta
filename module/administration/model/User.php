<?php
namespace Administration\Model;

use Application\Model\TableGatewayAdapter;

class User extends TableGatewayAdapter
{
	function getList($page = NULL, $max_page = 10)
	{
		$this->tableGateway->getSql()->setTable('album');
		if (empty($page)) return $this->tableGateway->select();
		
		return $this->paginator('album', $page, $max_page);
	}
	
	function add($data)
	{
		$this->tableGateway->getSql()->setTable('album');
		$this->tableGateway->insert($data);
	}
}
