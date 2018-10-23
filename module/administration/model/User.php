<?php
namespace Administration\Model;

use Application\Model\TableGatewayAdapter;

class User
{
	function getList($page = NULL, $max_page = 10)
	{
		if (empty($page)) return TableGatewayAdapter::init('album')->select();
		
		return TableGatewayAdapter::paginator('album', $page, $max_page);
	}
	
	function add($data)
	{
		$this->tableGateway->getSql()->setTable('album');
		$this->tableGateway->insert($data);
	}
	
	public function delete($id)
	{
		TableGatewayAdapter::init('album')->delete(['id' => (int) $id]);
	}
	
	public function get($id)
	{
		$id = (int) $id;
		$rowset = TableGatewayAdapter::init('album')->select(['id' => $id]);
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
