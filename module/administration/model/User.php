<?php
namespace Administration\Model;

use Application\Model\Versa_Gateway_Adapter;

class User extends Versa_Gateway_Adapter
{
	public function getList($page = NULL, $max_page = 10)
	{
		if (empty($page)) return $this->init('tblUser')->select();
		
		$select = $this->select();
		$select->from(array('a' => 'tblUser'), array('*', 'user_name as login_name'))
				->join(array('b' => 'tblDeposit'), 'a.user_id=b.user_id', array('deposit_value'))
				->join(array('c' => 'tblGroup'), 'a.group_id = c.group_id', array('group_name'))
				//->join(array('d' => 'tblUser'), 'a.user_create_by = d.user_id', array('d.user_name as create_by'))
				->where("a.user_status <> 'D'")
				->order('a.user_name DESC');
		//echo $select->getSqlString(); exit();
		
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
	
	public function update($id, $data)
	{
		$this->init('tblUser')->update($data, ['user_id' => $id]);
	}
	
	public function get($id)
	{
		$id = (int) $id;
		
		$select = $this->select()->from('tblUser')->where(['user_id' => $id]);
		//echo $select->getSqlString();
		
		$rowset = $this->init('tblUser')->selectWith($select);
		$row = $rowset->current();
		if (! $row) {
			throw new RuntimeException(sprintf(
				'Could not find row with identifier %d',
				$id
			));
		}

		return $row;
	}
	
	function updateLifeTime($id, $time) 
	{
		$data = array(
			'user_lifetime' => $time,
		);
		$this->update($id, $data);
	}
}
