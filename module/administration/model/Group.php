<?php
namespace Administration\Model;

use Application\Model\Versa_Gateway_Adapter;

class Group extends Versa_Gateway_Adapter
{
	function getAccess($group_id)
	{
		$select = $this->select()->from('tblGroup')->where(['group_id' => $group_id]);
		$rowset = $this->init('tblGroup')->selectWith($select)->current();
		$access = $rowset->group_access;
		
		return $access;
	}
	
	function getAccessAll()
	{
		$rowset = $this->init('tblProfile')->select();
		$array = NULL;
		foreach ($rowset as $value) {
			$array[] = $value->profile_code;
		}
		return $array;
	}
}
