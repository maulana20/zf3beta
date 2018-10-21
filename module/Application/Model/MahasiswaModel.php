<?php
namespace Application\Model;
use Application\Model\TableGatewayAdapter;

class MahasiswaModel extends TableGatewayAdapter
{
	function getList()
	{
		$this->tableGateway->getSql()->setTable('posts');
		return $this->tableGateway->select();
	}
}
