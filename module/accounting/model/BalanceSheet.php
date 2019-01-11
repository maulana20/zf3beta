<?php
namespace Accounting\Model;

use Application\Model\Versa_Gateway_Adapter;

class BalanceSheet extends Versa_Gateway_Adapter
{
	function getList()
	{
		$result = array();
		
		$select = $this->select();
		$select->from('tblGroupAccount')
				->where("groupaccount_type IN ('BS1', 'BS2')")
				->order('groupaccount_id ASC')
		;
		
		//echo $select->getSqlString(); exit();
		$list = $this->init('tblGroupAccount')->selectWith($select);
		
		foreach ($list as $value) {
			$result[] = (array) $value;
		}
		
		return $result;
	}
	
	function getCoaList($type)
	{
		$result = array();
		
		$select = $this->select();
		$select->from( ['a' => 'tblGroupAccount'] )
				->columns( [ '*', 'coa_list' => $this->expression("STUFF ((SELECT ',' + CONVERT(varchar(11), coa_id) + '|' + coa_code + '|' + coa_name FROM tblCoa as b WHERE a.groupaccount_id = b.groupaccount_id AND b.lod = 5 FOR XML PATH('')), 1, 1, '')") ] )
				->where( ['a.groupaccount_type' => $type] )
				->order('a.groupaccount_id ASC')
		;
		
		//echo $select->getSqlString(); exit();
		$list = $this->init('tblGroupAccount')->selectWith($select);
		
		foreach ($list as $value) {
			$result[] = (array) $value;
		}
		
		return $result;
	}
}
