<?php
namespace Administration\Model;

use Application\Model\Versa_Gateway_Adapter;

class Group extends Versa_Gateway_Adapter
{
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
