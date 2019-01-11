<?php
namespace Administration\Model;

use Application\Model\Versa_Gateway_Adapter;

class Group extends Versa_Gateway_Adapter
{
	function add($data)
	{
		$data['group_created'] = time();
		$data['group_status'] = 'A';
		
		$this->init('tblGroup')->insert($data);
	}
	
	function update($id, $data)
	{
		$this->init('tblGroup')->update($data, ['group_id' => $id]);
	}
	
	function delete($id)
	{
		$this->update($id, ['group_status' => 'D']);
	}
	
	function getAccess($group_id)
	{
		$select = $this->select();
		$select->from('tblGroup')
				->where( ['group_id' => $group_id] )
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblGroup')->selectWith($select)->current();
		
		return $rowset->group_access;
	}
	
	function getAccessAll()
	{
		$result = array();
		
		$rowset = $this->init('tblProfile')->select();
		foreach ($rowset as $value) $result[] = $value->profile_code;
		
		return $result;
	}
	
	function getList()
	{
		$result = array();
		
		$select = $this->select();
		$select->from('tblGroup')
				->columns( ['group_id', 'group_code', 'group_name', 'group_created', 'group_status', 'group_access'] )
				->where("group_status <> 'D'")
				->order('group_name')
		;
		
		// echo $select->getSqlString(); exit();
		$data = $this->init('tblGroup')->selectWith($select);
		foreach ($data as $value) $result[] = (array) $value;
		
		return $result;
	}
	
	function getId($group_name)
	{
		$select = $this->select();
		$select->from('tblGroup')
				->columns( ['group_id'] )
				->where( ['group_name' => ucwords(strtolower($group_name)), 'group_status' => 'A'] )
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblGroup')->selectWith($select)->current();
		
		return $rowset->group_id;
	}
	
	function getRow($group_id)
	{
		$select = $this->select();
		$select->from('tblGroup')
				->where( ['group_id' => $group_id] )
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblGroup')->selectWith($select)->current();
		
		return (array) $rowset;
	}
	
	function getCount()
	{
		$select = $this->select();
		$select->from('tblGroup')
				->columns( ['count' => $this->expression('COUNT(*)')] )
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblGroup')->selectWith($select)->current();
		
		return $rowset->count;
	}
	
	function isAlready($name)
	{
		$select = $this->select();
		$select->from('tblGroup')
				->columns( ['count' => $this->expression('COUNT(*)')] )
				->where( ['group_name' => ucwords(strtolower($name))] )
				->where( ['group_status' => 'A'] )
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblGroup')->selectWith($select)->current();
		
		return $rowset->count > 0;
	}
	
	function isAlreadyCode($code)
	{
		$select = $this->select();
		$select->from('tblGroup')
				->columns( ['count' => $this->expression('COUNT(*)')] )
				->where( ['group_code' => ucwords(strtolower($code))] )
				->where( ['group_status' => 'A'] )
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblGroup')->selectWith($select)->current();
		
		return $rowset->count > 0;
	}
	
	function isAnyUserInGroup($group_id)
	{
		$select = $this->select();
		$select->from('tblUser')
				->columns( ['count' => $this->expression('COUNT(*)')] )
				->where( ['group_id' => $group_id] )
				->where("user_status <> 'D'")
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblUser')->selectWith($select)->current();
		
		return $rowset->count > 0;
	}
}
