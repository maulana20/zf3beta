<?php
namespace Accounting\Model;

use Application\Model\Versa_Gateway_Adapter;

class Coa extends Versa_Gateway_Adapter
{
	public function add($data)
	{
		$data['coa_status'] = 'A';
		
		$this->init('tblCoa')->insert($data);
	}
	
	function update($id, $data)
	{
		$this->init('tblCoa')->update($data, ['coa_id' => $id]);
	}
	
	public function getList()
	{
		$result = NULL;
		
		$select = $this->select();
		$select->from( ['a' => 'tblCoa'] )
				->where( ['a.coa_status' => 'A'] )
		;
		
		// echo $select->getSqlString(); exit();
		$list = $this->init('tblCoa')->selectWith($select);
		
		$result[] = array('coa_id' => 0, 'coa_code' => 'all', 'coa_name' => '-');
		foreach ($list as $value) {
			$result[] = $value;
		}
		
		return $result;
	}
	
	public function getListCashBank()
	{
		$result = NULL;
		
		$select = $this->select();
		$select->from( ['a' => 'tblCoa'] )
				->where( ['a.coa_status' => 'A'] )
				->where("groupaccount_id IN (1, 2)")
		;
		
		// echo $select->getSqlString(); exit();
		$list = $this->init('tblCoa')->selectWith($select);
		
		foreach ($list as $value) {
			$result[] = $value;
		}
		
		return $result;
	}
	
	public function getRow($id)
	{
		$select = $this->select();
		$select->from( ['a' => 'tblCoa'] )
				->join( ['b' => 'tblGroupAccount'], 'a.groupaccount_id=b.groupaccount_id', ['groupaccount_name' => 'groupaccount_name'] )
				->join( ['c' => 'tblUser'], 'c.user_id = a.user_id', ['user_name' => 'user_name'] )
				->where( ['a.coa_id' => $id] )
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblCoa')->selectWith($select)->current();
		
		return (array) $rowset;
	}
	
	public function isAlready($code)
	{
		$rowset = NULL;
		$select = $this->select();
		$select->from( ['a' => 'tblCoa'] )
				->columns( ['count' => $this->expression('COUNT(*)')] )
				->where( ['a.coa_code' => $code] )
				->where("a.coa_status <> 'D'")
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblCoa')->selectWith($select)->current();
		
		return ($rowset['count'] > 0);
	}
}
