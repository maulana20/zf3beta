<?php
namespace Administration\Model;

use Application\Model\Versa_Gateway_Adapter;
use Administration\Model\Password;

class User extends Versa_Gateway_Adapter
{
	public function add($data)
	{
		$pass = new Password();
		$data['password'] = $pass->encode($data['password']);
		$data['password_attempt'] = -1;
		$data['user_created'] = time();
		$data['user_status'] = 'A';
		
		$this->init('tblUser')->insert($data);
	}
	
	public function update($id, $data)
	{
		$this->init('tblUser')->update($data, ['user_id' => $id]);
	}
	
	public function active($id)
	{
		$this->update($id, ['user_status' => 'A']);
	}
	
	public function inActive($id)
	{
		$this->update($id, ['user_status' => 'I']);
	}
	
	function delete($id) 
	{
		$this->update($id, ['user_status' => 'D']);
	}
	
	function isUserPassword($name, $password, $user_status = 'A')
	{
		$pass = new Password();
		$select = $this->select();
		$select->from('tblUser')
				->columns( ['count' => $this->expression('COUNT(*)')] )
				->where( [
							'user_name' => ucwords(strtolower($name)),
							'password' =>  $pass->encode($password),
							'user_status' => $user_status
						] )
		;
		
		//echo $select->getSqlString(); exit();
		$rowset = $this->init('tblUser')->selectWith($select)->current();
		
		return ($rowset->count > 0);
	}
	
	function getId($name, $user_status = 'A')
	{
		$select = $this->select();
		$select->from('tblUser')
				->where( [
							'user_name' => ucwords(strtolower($name)),
							'user_status' => $user_status
						] )
		;
		
		//echo $select->getSqlString(); exit();
		$rowset = $this->init('tblUser')->selectWith($select)->current();
		
		return (!empty($rowset->user_id)) ? $rowset->user_id : NULL;
	}
	
	public function getList($page = NULL, $max_page = 10)
	{
		$result = NULL;
		if (empty($page)) return $this->init('tblUser')->select();
		$select = $this->select();
		$select->from( ['a' => 'tblUser'] )
				->columns( ['*', 'login_name' => $this->expression('a.user_name')] )
				->join( ['b' => 'tblGroup'], 'a.group_id = b.group_id', ['group_name' => 'group_name', 'group_code' => 'group_code'] )
				->join( ['c' => 'tblUser'], 'a.user_create_by = c.user_id', ['create_by' => 'user_name'] )
				->where("a.user_status <> 'D'")
				->order('a.user_name DESC')
		;
		
		//echo $select->getSqlString(); exit();
		$pagination = $this->paginator($select, $page, $max_page);
		foreach ($pagination as $value) {
			$value->password = '';
			$result[] = (array) $value;
		}
		
		return $result;
	}
	
	function getCount()
	{
		$select = $this->select();
		$select->from('tblUser')
				->columns( ['count' => $this->expression('COUNT(*)')] )
				->where("user_status <> 'D'")
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblUser')->selectWith($select)->current();
		
		return $rowset->count;
	}
	
	public function getRow($id)
	{
		$select = $this->select();
		$select->from('tblUser')
				->where(['user_id' => $id])
		;
		
		// echo $select->getSqlString();
		$rowset = $this->init('tblUser')->selectWith($select)->current();
		if (empty($rowset)) return NULL;
		$rowset->password = '';
		
		return ($rowset->user_status != 'D') ? (array) $rowset : NULL;
	}
	
	function getCountSearch($user_id, $search_txt, $column_choice, $partial, $create_start_date, $status_choice)
	{
		if (!isset($search_txt)) return NULL;
		
		$search_txt = explode("\n", $search_txt);
		$addChr = ($partial == 1) ? '%' : '';
		
		switch ($column_choice) {
			case 'user_master' : $user_name = 'a.user_name'; break;
			case 'group_name' : $user_name = 'b.group_name'; break; 
			case 'create_by' : $user_name = 'c.user_name'; break;
			default : $user_name = 'a.' . $column_choice;
		}
		
		$select = $this->select();
		$select->from( ['a' => 'tblUser'] )
				->columns( ['count' => $this->expression('COUNT(*)')] )
				->join( ['b' => 'tblGroup'], 'a.group_id = b.group_id', [] )
				->join( ['c' => 'tblUser'], 'a.user_create_by = c.user_id', [] )
		;
		
		if ($column_choice == 'user_created') {
			$select->where($user_name . ' > ' . $create_start_date);
		} else if ($column_choice == 'user_login') {
			$select->where( [$user_name => NULL] );
		} else if ($partial == 1) {
			$where = $this->where();
			foreach ($search_txt as $val) {
				if ($column_choice == 'user_id') {
					if (strlen($val) > 1) {
						if (substr($val, 0, 1) == '%') {
							$val = ltrim(ltrim(trim($val), '%'), 0);
						} else {
							$val = ltrim(trim($val), 0);
						}
					}
					$where->OR->like($user_name, $addChr . $val . $addChr);
				} else {
					$where->OR->like($user_name, $addChr . $val . $addChr);
				}
			}
			$select->where( $where );
		} else {
			$where = $this->where();
			foreach ($search_txt as $val) $where->OR->like($user_name, $addChr . $val . $addChr);
			$select->where( $where );
		}
		switch ($status_choice) {
			case 'any' : $select->where("a.user_status <> 'D'"); break;
			default : $select->where( ['a.user_status' => $status_choice] );
		}
		//echo $select->getSqlString(); exit();
		
		$rowset = $this->init('tblUser')->selectWith($select)->current();
		
		return $rowset->count;
	}
	
	function getListSearch($user_id, $search_txt, $column_choice, $partial, $create_start_date, $status_choice, $page, $max_page)
	{
		$result = NULL;
		if (!isset($search_txt)) return $result;
		
		$search_txt = explode("\n", $search_txt);
		$addChr = ($partial == 1) ? '%' : '';
		
		switch ($column_choice) {
			case 'user_name' : $user_name = 'a.user_name'; break;
			case 'group_name' : $user_name = 'b.group_name'; break; 
			case 'create_by' : $user_name = 'c.user_name'; break;
			default : $user_name = 'a.' . $column_choice;
		}
		
		$select = $this->select();
		$select->from( ['a' => 'tblUser'] )
				->columns( ['*', 'login_name' => $this->expression('a.user_name')] )
				->join( ['b' => 'tblGroup'], 'a.group_id = b.group_id', ['group_name' => 'group_name', 'group_code' => 'group_code'] )
				->join( ['c' => 'tblUser'], 'a.user_create_by = c.user_id', ['create_by' => 'user_name'] )
		;
		
		if ($column_choice == 'user_created') {
			$select->where($user_name . ' > ' . $create_start_date);
		} else if ($column_choice == 'user_login') {
			$select->where( [$user_name => NULL] );
		} else if ($partial == 1) {
			$where = $this->where();
			foreach ($search_txt as $val) {
				if ($column_choice == 'user_id') {
					if (strlen($val) > 1) {
						if (substr($val, 0, 1) == '%') {
							$val = ltrim(ltrim(trim($val), '%'), 0);
						} else {
							$val = ltrim(trim($val), 0);
						}
					}
					$where->OR->like($user_name, $addChr . $val . $addChr);
				} else {
					$where->OR->like($user_name, $addChr . $val . $addChr);
				}
			}
			$select->where( $where );
		} else {
			$where = $this->where();
			foreach ($search_txt as $val) $where->OR->like($user_name, $addChr . $val . $addChr);
			$select->where( $where );
		}
		switch ($status_choice) {
			case 'any' : $select->where("a.user_status <> 'D'"); break;
			default : $select->where( ['a.user_status' => $status_choice] );
		}
		
		//echo $select->getSqlString(); exit();
		$list = (empty($page)) ? $this->init('tblUser')->selectWith($select) : $this->paginator($select, $page, $max_page);
		foreach ($list as $value) {
			$value->password = '';
			$result[] = (array) $value;
		}
		
		return $result;
	}
	
	function isBlocked($name)
	{
		$select = $this->select();
		$select->from('tblUser')
			->where( ['user_name' => ucwords($name)] )
			->where( [$this->where()->equalTo('user_status', 'I')->OR->equalTo('user_status', 'B')] )
		;
		
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
		
		$this->init('tblUser')->update($data, ['user_id' => $id]);
	}
	
	function isAlready($name)
	{
		$select = $this->select();
		$select->from('tblUser')
				->columns( ['count' => $this->expression('COUNT(*)')] )
				->where( ['user_name' => ucwords(strtolower($name))] )
				->where("user_status <> 'D'")
		;
		
		//echo $select->getSqlString(); exit();
		$rowset = $this->init('tblUser')->selectWith($select)->current();
		
		return ($rowset['count'] > 0);
	}
	
	function isOnLogin($id)
	{
		if (empty($id)) return false;
		$select = $this->select();
		$select->from('tblUser')
				->where( ['user_id' => $id] )
		;
		
		//echo $select->getSqlString();
		$rowset = $this->init('tblUser')->selectWith($select)->current();
		
		return ($rowset->user_lifetime > time());
	}
	
	function incPasswordAttempt($id)
	{
		$select = $this->select();
		$select->from('tblUser')
				->where( ['user_id' => $id] );
		//echo $select->getSqlString();
		
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
