<?php
namespace Application\Model;
use Application\Model\TableGatewayAdapter;

class Mahasiswa extends TableGatewayAdapter
{
	function getList()
	{
		$this->tableGateway->getSql()->setTable('posts');
		return $this->tableGateway->select();
	}
}
