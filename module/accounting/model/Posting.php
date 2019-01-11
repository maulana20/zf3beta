<?php
namespace Accounting\Model;

use Application\Model\Versa_Gateway_Adapter;

class Posting extends Versa_Gateway_Adapter
{
	public function add($data)
	{
		$this->init('tblPosting')->insert($data);
	}
	
	public function update($id, $data)
	{
		$this->init('tblPosting')->update($data, ['posting_id' => $id]);
	}
	
	public function getId($coa, $begin)
	{
		$select = $this->select();
		$select->from( ['a' => 'tblPosting'] )
				->where( ['a.coa_id' => $coa, 'a.period_begin' => $begin] )
		;
		
		//echo $select->getSqlString(); exit();
		$rowset = $this->init('tblPosting')->selectWith($select)->current();
		
		return (!empty($rowset->posting_id)) ? $rowset->posting_id : NULL;
	}
}
