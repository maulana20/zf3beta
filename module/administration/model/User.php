<?php
namespace Administration\Model;

use Application\Model\Versa_Gateway_Adapter;

class User extends Versa_Gateway_Adapter
{
	public function getList($page = NULL, $max_page = 10)
	{
		if (empty($page)) return $this->init('album')->select();
		
		$select = $this->select();
		$select->from('album');
		
		return $this->paginator($select, $page, $max_page);
	}
	
	public function add($data)
	{
		$this->init('album')->insert($data);
	}
	
	public function delete($id)
	{
		$this->init('album')->delete(['id' => (int) $id]);
	}
	
	public function update($data, $id)
	{
		$this->init('album')->update($data, ['id' => $id]);
	}
	
	public function get($id)
	{
		$id = (int) $id;
		
		$select = $this->select()->from('album')->where(['id' => $id]);
		$rowset = $this->init('album')->selectWith($select);
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
