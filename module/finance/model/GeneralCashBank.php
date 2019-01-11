<?php
namespace Finance\Model;

use Application\Model\Versa_Gateway_Adapter;

class GeneralCashBank extends Versa_Gateway_Adapter
{
	public function getCount()
	{
		$select = $this->select();
		$select->from( ['a' => 'tblGeneralCashBank'] )
				->columns( ['count' => $this->expression('COUNT(*)')] )
				->join( ['b' => 'tblFinancialTrans'], 'b.financialtrans_id = a.financialtrans_id', [] )
				->join( ['c' => 'tblUser'], 'c.user_id = b.user_id', [] )
				->where('b.financialtrans_date > ' . strtotime('-3 month'))
				->where('b.financialtrans_date < ' . time())
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblGeneralCashBank')->selectWith($select)->current();
		
		return $rowset->count;
	}
	
	public function getList($page = NULL, $max_page = 10)
	{
		$result = NULL;
		$select = $this->select();
		$select->from( ['a' => 'tblGeneralCashBank'] )
				->columns( ['generalcashbank_id' => $this->expression('a.generalcashbank_id'),
							'period_begin' => $this->expression('b.period_begin'),
							'financialtrans_date' => $this->expression('b.financialtrans_date')
				] )
				->join( ['b' => 'tblFinancialTrans'], 'b.financialtrans_id = a.financialtrans_id', ['vou' => 'vou'] )
				->join( ['c' => 'tblUser'], 'c.user_id = b.user_id', ['user_name' => 'user_name'] )
				->where('b.financialtrans_date > ' . strtotime('-3 month'))
				->where('b.financialtrans_date < ' . time())
		;
		
		// echo $select->getSqlString(); exit();
		$pagination = $this->paginator($select, $page, $max_page);
		foreach ($pagination as $value) {
			$value->password = '';
			$result[] = (array) $value;
		}
		
		return $result;
	}
	
	public function getRow($id)
	{
		$select = $this->select();
		$select->from( ['a' => 'tblGeneralCashBank'] )
				->columns( ['generalcashbank_id' => $this->expression('a.generalcashbank_id'),
							'financialtrans_id' => $this->expression('a.financialtrans_id'),
							'glanalysis_position' => $this->expression("(CASE WHEN a.generalcashbank_position = 'I' THEN 'D' ELSE 'C' END)"),
							'coa_id' => $this->expression("(SELECT TOP 1 coa_from FROM tblGlAnalysis as e WHERE e.financialtrans_id = a.financialtrans_id AND e.glanalysis_position = (CASE WHEN a.generalcashbank_position = 'I' THEN 'D' ELSE 'C' END))"),
							'coa_name' => $this->expression("(SELECT TOP 1 coa_code + ' ' + coa_name FROM tblGlAnalysis as e INNER JOIN tblCoa as f ON f.coa_id = e.coa_from WHERE e.financialtrans_id = a.financialtrans_id AND e.glanalysis_position = (CASE WHEN a.generalcashbank_position = 'I' THEN 'D' ELSE 'C' END))"),
							'glanalysis_desc' => $this->expression("(SELECT TOP 1 glanalysis_desc FROM tblGlAnalysis as e WHERE e.financialtrans_id = a.financialtrans_id AND e.glanalysis_position = (CASE WHEN a.generalcashbank_position = 'I' THEN 'D' ELSE 'C' END))")
							//'glanalysis_value' => $this->expression("(SELECT SUM(glanalysis_value) FROM tblGlAnalysis as e WHERE e.financialtrans_id = a.financialtrans_id AND e.glanalysis_position = (CASE WHEN a.generalcashbank_position = 'I' THEN 'D' ELSE 'C' END))")
				] )
				->join( ['b' => 'tblFinancialTrans'], 'b.financialtrans_id = a.financialtrans_id', ['period_begin' => 'period_begin', 'financialtrans_date' => 'financialtrans_date', 'vou' => 'vou'] )
				->join( ['c' => 'tblUser'], 'c.user_id = b.user_id', ['user_name' => 'user_name'] )
				->join( ['d' => 'tblPeriod'], 'd.period_begin = b.period_begin', ['period_status' => 'period_status'] )
				->where( ['a.generalcashbank_id' => $id] )
		;
		
		// echo $this->select->getSqlString(); exit();
		$rowset = $this->init('tblGeneralCashBank')->selectWith($select)->current();
		
		return (array) $rowset;
	}
}
