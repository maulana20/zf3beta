<?php
namespace Accounting\Model;

use Application\Model\Versa_Gateway_Adapter;

class TrialBalance extends Versa_Gateway_Adapter
{
	public function getBeginSearch($start_date, $end_date)
	{
		$result = NULL;
		$select = $this->select();
		$select->from( ['a' => 'tblGroupAccount'] )
				->columns( ['groupaccount_id' => $this->expression('a.groupaccount_id'),
							'groupaccount_name' => $this->expression('a.groupaccount_name'),
							'groupaccount_type' => $this->expression('a.groupaccount_type'),
							// awal periode + balance dari start_date yaitu tanggal awal sampai start_date (debet - kredit : glanalysis (balance awal))
							'begining' => $this->expression("ISNULL((SELECT posting_balance FROM tblPosting as c WHERE c.coa_id=b.coa_id AND c.period_begin=" . date('Ym', strtotime("-1 month", $start_date)) . "), 0) + ISNULL((SELECT SUM(CASE WHEN e.glanalysis_position = 'C' THEN e.glanalysis_value ELSE e.glanalysis_value * -1 END) AS 'balance' FROM tblFinancialTrans as d INNER JOIN tblGlAnalysis as e ON e.financialtrans_id=d.financialtrans_id WHERE d.financialtrans_date > " . strtotime(date('01 M Y', $start_date) . ' 00:00:00') . " AND d.financialtrans_date < " . strtotime(date('d M Y', strtotime('-1 day', $start_date)) . ' 23:59:59') . " AND e.coa_to=b.coa_id), 0)")
				] )
				->join( ['b' => 'tblCoa'], 'b.groupaccount_id = a.groupaccount_id', ['coa_id' => 'coa_id', 'coa_code' => 'coa_code', 'coa_name' => 'coa_name'] )
				->group( ['a.groupaccount_id', 'a.groupaccount_name', 'a.groupaccount_type', 'b.coa_id', 'b.coa_code', 'b.coa_name'] )
				->order('a.groupaccount_id ASC')
		;
		
		// echo $select->getSqlString(); exit();
		$list = $this->init('tblGroupAccount')->selectWith($select);
		
		foreach ($list as $value) {
			$tb_search = array();
			$tb_search = $this->getListSearch($start_date, $end_date, $value->coa_code);
			$result[] = (!empty($tb_search)) ? $tb_search : $value;
		}
		
		return $result;
	}
	
	public function getListSearch($start_date, $end_date, $coa_code)
	{
		$result = NULL;
		$select = $this->select();
		$select->from( ['a' => 'tblGroupAccount'] )
				->columns( ['groupaccount_id' => $this->expression('a.groupaccount_id'),
							'groupaccount_name' => $this->expression('a.groupaccount_name'),
							'groupaccount_type' => $this->expression('a.groupaccount_type'),
							// awal periode + balance dari start_date yaitu tanggal awal sampai start_date (debet - kredit : glanalysis (balance awal))
							'begining' => $this->expression("ISNULL((SELECT posting_balance FROM tblPosting as e WHERE e.coa_id=b.coa_id AND e.period_begin=" . date('Ym', strtotime("-1 month", $start_date)) . "), 0) + ISNULL((SELECT SUM(CASE WHEN g.glanalysis_position = 'C' THEN g.glanalysis_value ELSE g.glanalysis_value * -1 END) AS 'balance' FROM tblFinancialTrans as f INNER JOIN tblGlAnalysis as g ON g.financialtrans_id=f.financialtrans_id WHERE f.financialtrans_date > " . strtotime(date('01 M Y', $start_date) . ' 00:00:00') . " AND f.financialtrans_date < " . strtotime(date('d M Y', strtotime('-1 day', $start_date)) . ' 23:59:59') . " AND g.coa_to=b.coa_id), 0)"),
							'glanalysis_debet' => $this->expression("SUM(CASE WHEN c.glanalysis_position = 'C' THEN c.glanalysis_value ELSE 0 END)"),
							'glanalysis_credit' => $this->expression("SUM(CASE WHEN c.glanalysis_position = 'D' THEN c.glanalysis_value ELSE 0 END)"),
							// awal periode + balance dari start_date yaitu tanggal awal sampai start_date (debet - kredit : glanalysis (balance awal)) + perhitungan debet kredit pada tanggal berikutnya hingga end_date (debet - kredit : glanalysis (balance akhir))
							'ending' => $this->expression("ISNULL((SELECT posting_balance FROM tblPosting as e WHERE e.coa_id=b.coa_id AND e.period_begin=" . date('Ym', strtotime("-1 month", $start_date)) . "), 0) + ISNULL((SELECT SUM(CASE WHEN g.glanalysis_position = 'C' THEN g.glanalysis_value ELSE g.glanalysis_value * -1 END) AS 'balance' FROM tblFinancialTrans as f INNER JOIN tblGlAnalysis as g ON g.financialtrans_id=f.financialtrans_id WHERE f.financialtrans_date > " . strtotime(date('01 M Y', $start_date) . ' 00:00:00') . " AND f.financialtrans_date < " . strtotime(date('d M Y', strtotime('-1 day', $start_date)) . ' 23:59:59') . " AND g.coa_to=b.coa_id), 0) + SUM(CASE WHEN c.glanalysis_position = 'C' THEN c.glanalysis_value ELSE c.glanalysis_value * -1 END)")
				] )
				->join( ['b' => 'tblCoa'], 'b.groupaccount_id = a.groupaccount_id', ['coa_id' => 'coa_id', 'coa_code' => 'coa_code', 'coa_name' => 'coa_name'] )
				->join( ['c' => 'tblGlAnalysis'], 'c.coa_to = b.coa_id', [] )
				->join( ['d' => 'tblFinancialTrans'], 'd.financialtrans_id = c.financialtrans_id', [] )
				->where('d.financialtrans_date > ' . strtotime(date('d M Y', $start_date) . ' 00:00:00'))
				->where('d.financialtrans_date < ' . strtotime(date('d M Y', $end_date) . ' 23:59:59'))
				->group( ['a.groupaccount_id', 'a.groupaccount_name', 'a.groupaccount_type', 'b.coa_id', 'b.coa_code', 'b.coa_name'] )
		;
		
		if (!empty($coa_code)) $select->where( ['b.coa_code' => $coa_code] );
		
		// echo $select->getSqlString(); exit();
		//$list = $this->init('tblGroupAccount')->selectWith($select);
		
		//foreach ($list as $value) {
		//	$result[] = $value;
		//}
		
		//return $result;
		
		$rowset = $this->init('tblGroupAccount')->selectWith($select)->current();
		
		return (array) $rowset;
	}
}
