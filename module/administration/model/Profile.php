<?php
namespace Administration\Model;

use Application\Model\Versa_Gateway_Adapter;

class Profile extends Versa_Gateway_Adapter
{
	function add($data)
	{
		$this->init('tblProfile')->insert($data);
	}
	
	function isAlready($profile_code)
	{
		$select = $this->select();
		$select->from('tblProfile')
				->columns( ['count' => $this->expression('COUNT(*)')] )
				->where( ['profile_code' => $profile_code] )
		;
		
		//echo $select->getSqlString(); exit();
		$rowset = $this->init('tblProfile')->selectWith($select)->current();
		
		return ($rowset->count > 0);
	}
}
