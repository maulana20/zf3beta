<?php
namespace Administration\Model;

use Application\Model\Versa_Gateway_Adapter;
use Administration\Model\Password;

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
	
	function getId($name, $user_status = 'A')
	{
		$select = $this->select()->from('tblUser')->where(['user_name' => ucwords(strtolower($name)), 'user_status' => $user_status]);
		$rowset = $this->init('tblUser')->selectWith($select)->current();
		
		return (!empty($rowset->user_id)) ? $rowset->user_id : NULL;
	}
	
	function isUserPassword($name, $password, $user_status = 'A')
	{
		$pass = new Password();
		$select = $this->select()->columns(array('count' => $this->expression('COUNT(*)')))->from('tblUser')->where(['user_name' => ucwords(strtolower($name)), 'password' =>  $pass->encode($password), 'user_status' => $user_status]);
		//echo $select->getSqlString(); exit();
		$rowset = $this->init('tblUser')->selectWith($select)->current();
		
		return ($rowset->count > 0);
	}
	
	public function getList($page = NULL, $max_page = 10)
	{
		$result = NULL;
		if (empty($page)) return $this->init('tblUser')->select();
		
		$select = $this->select();
		$select->from(array('a' => 'tblUser'), array('*', 'user_name as login_name'))
				->join(array('b' => 'tblDeposit'), 'a.user_id=b.user_id', array('deposit_value'))
				->join(array('c' => 'tblGroup'), 'a.group_id = c.group_id', array('group_name'))
				//->join(array('d' => 'tblUser'), 'a.user_create_by = d.user_id', array('d.user_name as create_by'))
				->where("a.user_status <> 'D'")
				->order('a.user_name DESC');
		//echo $select->getSqlString(); exit();
		
		$pagination = $this->paginator($select, $page, $max_page);
		foreach ($pagination as $value) {
			$result[] = (array) $value;
		}
		
		return $result;
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
		
		return (!empty($rowset->contact_detail)) ? $rowset->contact_detail : NULL;
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
	
	function getContact($id)
	{
		$select = $this->select()->from('tblContact')->where(['user_id' => $id]);
		$rowset = $this->init('tblDeposit')->selectWith($select)->current();
		
		return (array) $rowset;
	}
	
	function isBlocked($name)
	{
		$select = $this->select();
		$select->from('tblUser')
			->where( ['user_name' => ucwords($name)] )
			->where( [$this->where()->equalTo('user_status', 'I')->OR->equalTo('user_status', 'B')] );
		//echo $select->getSqlString();
		$rowset = $this->init('tblDeposit')->selectWith($select)->current();
		
		$user_name = (!empty($rowset->user_name)) ? $rowset->user_name : NULL;
		if (empty($user_name)) return $user_name;
		
		return strtolower($user_name) == strtolower($name);
	}
	
	function updateLifeTime($id, $time) 
	{
		$data = array();
		$data['user_lifetime'] = $time;
		
		$this->update($id, $data);
	}
	
	function isOnLogin($id)
	{
		if (empty($id)) return false;
		$select = $this->select()->from('tblUser')->where(['user_id' => $id]);
		$rowset = $this->init('tblUser')->selectWith($select)->current();
		
		return ($rowset->user_lifetime > time());
	}
	
	function incPasswordAttempt($id)
	{
		$select = $this->select()->from('tblUser')->where(['user_id' => $id]);
		$rowset = $this->init('tblUser')->selectWith($select)->current();
		
		$password_attempt = $rowset->password_attempt;
		$user_status = NULL;
		if (($rowset->password_attempt < 0) && ($rowset->password_attempt >= -10)) {
			$password_attempt--;
		} else if (($rowset->password_attempt >= 0) && ($rowset->password_attempt <= 9)) {
			$password_attempt++;
		} else {
			$password_attempt = 10;
			$user_status = 'B';
		}
		
		$data = array();
		$data['password_attempt'] = $password_attempt;
		if (!empty($user_status)) $data['user_status'] = $user_status;
		$this->init('tblUser')->update($data, ['user_id' => $id]);
		
		return false;
	}
}
