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
	
	public function delete($id)
	{
		$this->tableGateway->getSql()->setTable('album');
		$this->tableGateway->delete(['id' => (int) $id]);
	}
	
	public function get($id)
	{
		$id = (int) $id;
		$this->tableGateway->getSql()->setTable('album');
		$rowset = $this->tableGateway->select(['id' => $id]);
		$row = $rowset->current();
		if (! $row) {
			throw new RuntimeException(sprintf(
				'Could not find row with identifier %d',
				$id
			));
		}

		return $row;
	}
}
