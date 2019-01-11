<?php
namespace Finance\Model;

use Application\Model\Versa_Gateway_Adapter;

class InterCashBank extends Versa_Gateway_Adapter
{
	public function getCount()
	{
		$select = $this->select();
		$select->from( ['a' => 'tblInterCashBank'] )
				->columns( ['count' => $this->expression('COUNT(*)')] )
				->join( ['b' => 'tblFinancialTrans'], 'b.financialtrans_id = a.financialtrans_out', [] )
				->join( ['c' => 'tblFinancialTrans'], 'c.financialtrans_id = a.financialtrans_in', [] )
				->where('b.financialtrans_date > ' . strtotime('-3 month'))
				->where('b.financialtrans_date < ' . time())
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblInterCashBank')->selectWith($select)->current();
		
		return $rowset->count;
	}
	
	public function getList($page = NULL, $max_page = 10)
	{
		$result = NULL;
		$select = $this->select();
		$select->from( ['a' => 'tblInterCashBank'] )
				->columns( ['intercashbank_id' => $this->expression('a.intercashbank_id'),
							'period_begin' => $this->expression('b.period_begin'),
							'financialtrans_date' => $this->expression('b.financialtrans_date'),
							'glanalysis_value' => $this->expression("(SELECT TOP 1 e.glanalysis_value FROM tblGlAnalysis as e WHERE e.financialtrans_id = b.financialtrans_id)"),
							'glanalysis_desc' => $this->expression("(SELECT TOP 1 e.glanalysis_desc FROM tblGlAnalysis as e WHERE e.financialtrans_id = b.financialtrans_id)")
				] )
				->join( ['b' => 'tblFinancialTrans'], 'b.financialtrans_id = a.financialtrans_out', ['vou_out' => 'vou'] )
				->join( ['c' => 'tblFinancialTrans'], 'c.financialtrans_id = a.financialtrans_in', ['vou_in' => 'vou'] )
				->join( ['d' => 'tblUser'], 'd.user_id = b.user_id', ['user_name' => 'user_name'] )
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
		$select->from( ['a' => 'tblInterCashBank'] )
				->columns( ['*',
							'period_begin' => $this->expression('b.period_begin'),
							'financialtrans_date' => $this->expression('b.financialtrans_date'),
							'financialtrans_date' => $this->expression('b.financialtrans_date'),
							'coa_out_name' => $this->expression("(SELECT TOP 1 f.coa_code + ' ' + f.coa_name FROM tblGlAnalysis as e INNER JOIN tblCoa as f ON f.coa_id = e.coa_from WHERE e.financialtrans_id = a.financialtrans_out AND e.glanalysis_position = 'C')"),
							'coa_in_name' => $this->expression("(SELECT TOP 1 f.coa_code + ' ' + f.coa_name FROM tblGlAnalysis as e INNER JOIN tblCoa as f ON f.coa_id = e.coa_from WHERE e.financialtrans_id = a.financialtrans_in AND e.glanalysis_position = 'D')"),
							'coa_out' => $this->expression("(SELECT TOP 1 coa_from FROM tblGlAnalysis AS e WHERE e.financialtrans_id = a.financialtrans_out AND e.glanalysis_position = 'C')"),
							'coa_in' => $this->expression("(SELECT TOP 1 coa_from FROM tblGlAnalysis AS e WHERE e.financialtrans_id = a.financialtrans_in AND e.glanalysis_position = 'D')"),
							'glanalysis_value' => $this->expression("(SELECT TOP 1 glanalysis_value FROM tblGlAnalysis AS e WHERE e.financialtrans_id = a.financialtrans_out)"),
							'glanalysis_desc' => $this->expression("(SELECT TOP 1 glanalysis_desc FROM tblGlAnalysis AS e WHERE e.financialtrans_id = a.financialtrans_out)")
				] )
				->join( ['b' => 'tblFinancialTrans'], 'b.financialtrans_id = a.financialtrans_out', ['vou_out' => 'vou'] )
				->join( ['c' => 'tblFinancialTrans'], 'c.financialtrans_id = a.financialtrans_in', ['vou_in' => 'vou'] )
				->join( ['d' => 'tblUser'], 'd.user_id = b.user_id', ['user_name' => 'user_name'] )
				->join( ['e' => 'tblPeriod'], 'e.period_begin = b.period_begin', ['period_status' => 'period_status'] )
				->where( ['a.intercashbank_id' => $id] )
		;
		
		// echo $select->getSqlString(); exit();
		$rowset = $this->init('tblInterCashBank')->selectWith($select)->current();
		
		return (array) $rowset;
	}
}
