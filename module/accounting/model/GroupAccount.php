<?php
namespace Accounting\Model;

use Application\Model\Versa_Gateway_Adapter;

class GroupAccount extends Versa_Gateway_Adapter
{
	public function getRow($id)
	{
		$rowset = NULL;
		$select = $this->select();
		$select->from( ['a' => 'tblGroupAccount'] )
				->where( ['a.groupaccount_id' => $id] )
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblGroupAccount')->selectWith($select)->current();
		
		return (array) $rowset;
	}
	
	public function getList()
	{
		$result = NULL;
		$select = $this->select();
		$select->from( ['a' => 'tblGroupAccount'] )
				->columns( [ '*', 'groupaccount_typename' => $this->expression("(CASE WHEN a.groupaccount_type = 'BS1' THEN 'activa' WHEN a.groupaccount_type = 'BS2' THEN 'passiva' ELSE '' END)") ] )
		;
		
		// echo $select->getSqlString(); exit();
		$list = $this->init('tblGroupAccount')->selectWith($select);
		
		foreach ($list as $value) {
			$result[] = $value;
		}
		
		return $result;
	}
}
