<?php
namespace Administration\Model;

use Application\Model\Versa_Gateway_Adapter;;

class Rules extends Versa_Gateway_Adapter
{
	function getList()
	{
		$result = NULL;
		
		$select = $this->select()->from('tblRules')->order('rules_code');
		$query = $this->init('tblContact')->selectWith($select);
		foreach ($query as $value) {
			$result[] = (array) $value;
		}
		
		return $result;
	}
}
