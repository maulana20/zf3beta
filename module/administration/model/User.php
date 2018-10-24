<?php
namespace Administration\Model;

use Application\Model\Versa_Gateway_Adapter;

class User extends Versa_Gateway_Adapter
{
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
	
	public function getRow($id)
	{
		$select = $this->select()->from('tblUser')->where(['user_id' => $id]);
		$rowset = $this->init('tblUser')->selectWith($select)->current();
		
		return ($rowset->user_status != 'D') ? (array) $rowset : NULL;
	}
	
	public function getEmail($id)
	{
		$select = $this->select()->from('tblContact')->where(['contacttype_id' => 1, 'user_id' => $id]);
		$rowset = $this->init('tblContact')->selectWith($select)->current();
		
		return $rowset->contact_detail;
	}
	
	function getPhone($id)
	{
		$result = array();
		
		$select = $this->select()->from('tblPhone')->where(['user_id' => $id])->order('phone_id');
		$query = $this->init('tblContact')->selectWith($select);
		foreach ($query as $value) {
			$result[] = ((array) $value);
		}
		
		return $result;
	}
	
	function getDeposit($id)
	{
		$select = $this->select()->from('tblDeposit')->where(['user_id' => $id]);
		$rowset = $this->init('tblDeposit')->selectWith($select)->current();
		
		return $rowset->deposit_value;
	}
	
	function updateLifeTime($id, $time) 
	{
		$data = array();
		$data['user_lifetime'] = $time;
		
		$this->update($id, $data);
	}
}
