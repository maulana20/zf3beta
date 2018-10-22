<?php
namespace Application\Model;
use Application\Model\TableGatewayAdapter;

class Mahasiswa extends TableGatewayAdapter
{
	function getList($page = NULL, $max_page = 10)
	{
		$this->tableGateway->getSql()->setTable('posts');
		if (empty($page)) return $this->tableGateway->select();
		
		return $this->paginator('posts', $page, $max_page);
	}
}
