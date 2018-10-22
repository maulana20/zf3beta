<?php
namespace Administration\Model;

use Application\Model\TableGatewayAdapter;

class User extends TableGatewayAdapter
{
	function getList($page = NULL, $max_page = 10)
	{
		$this->tableGateway->getSql()->setTable('user');
		if (empty($page)) return $this->tableGateway->select();
		
		return $this->paginator('user', $page, $max_page);
	}
}
