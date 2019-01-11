<?php
namespace Accounting\Model;

use Application\Model\Versa_Gateway_Adapter;

class Period extends Versa_Gateway_Adapter
{
	public function add($data)
	{
		$data['period_status'] = 'A';
		
		$this->init('tblPeriod')->insert($data);
	}
	
	public function update($begin, $data)
	{
		$this->init('tblPeriod')->update($data, ['period_begin' => $begin]);
	}
	
	public function getListByYear($year = NULL)
	{
		$result = NULL;
		if (empty($year)) $year = date('Y', time());
		$select = $this->select();
		$where = $this->where();
		$where->OR->like('a.period_begin', $year . '%');
		$select->from( ['a' => 'tblPeriod'] )
				->where($where)
				->order('a.period_begin ASC')
		;
		
		//echo $select->getSqlString(); exit();
		$list = $this->init('tblPeriod')->selectWith($select);
		
		foreach ($list as $value) {
			$value->period_month = date( 'F', strtotime( substr($value->period_begin, 0, 4) . '-' . substr($value->period_begin, 4, 2) ) );
			$value->period_year = date( 'Y', strtotime( substr($value->period_begin, 0, 4) . '-' . substr($value->period_begin, 4, 2) ) );
			$result[] = $value;
		}
		
		return $result;
	}
	
	public function isAlready($begin)
	{
		$select = $this->select();
		$select->from('tblPeriod')
				->columns( ['count' => $this->expression('COUNT(*)')] )
				->where( ['period_begin' => $begin] )
		;
		
		//echo $select->getSqlString(); exit();
		$rowset = $this->init('tblPeriod')->selectWith($select)->current();
		
		return ($rowset['count'] > 0);
	}
}
