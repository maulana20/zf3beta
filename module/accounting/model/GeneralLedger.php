<?php
namespace Accounting\Model;

use Application\Model\Versa_Gateway_Adapter;

class GeneralLedger extends Versa_Gateway_Adapter
{
	public function getListSearchJournal($start_date, $end_date)
	{
		$result = NULL;
		$select = $this->select();
		$select->from( ['a' => 'tblFinancialTrans'] )
				->columns( ['financialtrans_date' => $this->expression('a.financialtrans_date'),
							'financialtrans_id' => $this->expression('a.financialtrans_id'),
							'vou' => $this->expression('a.vou'),
							'period_begin' => $this->expression('a.period_begin'),
							'glanalysis_debet' => $this->expression("SUM(CASE WHEN b.glanalysis_position = 'D' THEN b.glanalysis_value ELSE 0 END)"),
							'glanalysis_credit' => $this->expression("SUM(CASE WHEN b.glanalysis_position = 'C' THEN b.glanalysis_value ELSE 0 END)")
				] )
				->join( ['b' => 'tblGlAnalysis'], 'b.financialtrans_id = a.financialtrans_id', [] )
				->join( ['c' => 'tblPeriod'], 'c.period_begin = a.period_begin', ['period_status' => 'period_status'] )
				->join( ['d' => 'tblCoa'], 'b.coa_from = d.coa_id', ['coa_from' => 'coa_code', 'coa_name' => 'coa_name'] )
				->where('a.financialtrans_date > ' . strtotime(date('d M Y', $start_date) . ' 00:00:00'))
				->where('a.financialtrans_date < ' . strtotime(date('d M Y', $end_date) . ' 23:59:59'))
				->group( ['d.coa_code', 'd.coa_name', 'a.period_begin', 'c.period_status', 'a.vou', 'a.financialtrans_id', 'a.financialtrans_date', 'b.glanalysis_desc', 'b.glanalysis_position'] )
				->order('financialtrans_id ASC, glanalysis_debet DESC')
		;
		
		// echo $select->getSqlString(); exit();
		$list = $this->init('tblFinancialTrans')->selectWith($select);
		
		foreach ($list as $value) {
			$result[] = $value;
		}
		
		return $result;
	}
	
	public function getListSearchJournalByTrans($data = array(), $coa_id = null)
	{
		$result = NULL;
		
		if (empty($data)) return $result;
		
		$select = $this->select();
		$select->from( ['a' => 'tblFinancialTrans'] )
				->columns( ['financialtrans_date' => $this->expression('a.financialtrans_date'),
							'financialtrans_id' => $this->expression('a.financialtrans_id'),
							'vou' => $this->expression('a.vou'),
							'period_begin' => $this->expression('a.period_begin'),
							'glanalysis_desc' => $this->expression('b.glanalysis_desc'),
							'glanalysis_debet' => $this->expression("SUM(CASE WHEN b.glanalysis_position = 'D' THEN b.glanalysis_value ELSE 0 END)"),
							'glanalysis_credit' => $this->expression("SUM(CASE WHEN b.glanalysis_position = 'C' THEN b.glanalysis_value ELSE 0 END)")
				] )
				->join( ['b' => 'tblGlAnalysis'], 'b.financialtrans_id = a.financialtrans_id', ['glanalysis_position' => 'glanalysis_position'] )
				->join( ['c' => 'tblPeriod'], 'c.period_begin = a.period_begin', ['period_status' => 'period_status'] )
				->join( ['d' => 'tblCoa'], 'b.coa_from = d.coa_id', ['coa_id' => 'coa_id', 'coa_code' => 'coa_code', 'coa_name' => 'coa_name'] )
				->where("a.financialtrans_id IN (" . implode(',', $data) . ")")
				->group( ['d.coa_id', 'd.coa_code', 'd.coa_name', 'a.period_begin', 'c.period_status', 'a.vou', 'a.financialtrans_id', 'a.financialtrans_date', 'b.glanalysis_desc', 'b.glanalysis_position'] )
				->order('financialtrans_id ASC, glanalysis_debet DESC')
		;
		
		if (!empty($coa_id)) $select->where( ['b.coa_to' => $coa_id] );
		
		// echo $select->getSqlString(); exit();
		$list = $this->init('tblFinancialTrans')->selectWith($select);
		
		foreach ($list as $value) {
			$result[] = $value;
		}
		
		return $result;
	}
	
	public function getListSearchReport($start_date, $end_date, $coa_code)
	{
		$result = NULL;
		$select = $this->select();
		$select->from( ['a' => 'tblFinancialTrans'] )
				->columns( ['financialtrans_date' => $this->expression('a.financialtrans_date'),
							'vou' => $this->expression('a.vou'),
							'period_begin' => $this->expression('a.period_begin'),
							// awal periode + balance dari start_date yaitu tanggal awal sampai start_date (debet - kredit : glanalysis (balance awal))
							'begining' => $this->expression("ISNULL((SELECT posting_balance FROM tblPosting as e WHERE e.coa_id=b.coa_to AND e.period_begin=" . date('Ym', strtotime("-1 month", $start_date)) . "), 0) + ISNULL((SELECT SUM(CASE WHEN g.glanalysis_position = 'C' THEN g.glanalysis_value ELSE g.glanalysis_value * -1 END) AS 'balance' FROM tblFinancialTrans as f INNER JOIN tblGlAnalysis as g ON g.financialtrans_id=f.financialtrans_id WHERE f.financialtrans_date > " . strtotime(date('01 M Y', $start_date) . ' 00:00:00') . " AND f.financialtrans_date < " . strtotime(date('d M Y', strtotime('-1 day', $start_date)) . ' 23:59:59') . " AND g.coa_to=b.coa_to), 0)"),
							'glanalysis_debet' => $this->expression("(CASE WHEN b.glanalysis_position = 'C' THEN b.glanalysis_value ELSE 0 END)"),
							'glanalysis_credit' => $this->expression("(CASE WHEN b.glanalysis_position = 'D' THEN b.glanalysis_value ELSE 0 END)"),
							// awal periode + balance dari start_date yaitu tanggal awal sampai start_date (debet - kredit : glanalysis (balance awal)) + perhitungan debet kredit pada tanggal berikutnya hingga end_date (debet - kredit : glanalysis (balance akhir))
							'ending' => $this->expression("ISNULL((SELECT posting_balance FROM tblPosting as e WHERE e.coa_id=b.coa_to AND e.period_begin=" . date('Ym', strtotime("-1 month", $start_date)) . "), 0) + ISNULL((SELECT SUM(CASE WHEN g.glanalysis_position = 'C' THEN g.glanalysis_value ELSE g.glanalysis_value * -1 END) AS 'balance' FROM tblFinancialTrans as f INNER JOIN tblGlAnalysis as g ON g.financialtrans_id=f.financialtrans_id WHERE f.financialtrans_date > " . strtotime(date('01 M Y', $start_date) . ' 00:00:00') . " AND f.financialtrans_date < " . strtotime(date('d M Y', strtotime('-1 day', $start_date)) . ' 23:59:59') . " AND g.coa_to=b.coa_to), 0) + SUM(CASE WHEN b.glanalysis_position = 'C' THEN b.glanalysis_value ELSE b.glanalysis_value * -1 END) OVER (PARTITION BY b.coa_to ORDER BY b.glanalysis_id)")
				] )
				->join( ['b' => 'tblGlAnalysis'], 'b.financialtrans_id = a.financialtrans_id', ['glanalysis_desc' => 'glanalysis_desc'] )
				->join( ['c' => 'tblPeriod'], 'c.period_begin = a.period_begin', ['period_status' => 'period_status'] )
				->join( ['d' => 'tblCoa'], 'd.coa_id = b.coa_to', ['coa_to' => 'coa_code'] )
				->join( ['e' => 'tblCoa'], 'e.coa_id = b.coa_from', ['coa_from' => 'coa_code'] )
				->where('a.financialtrans_date > ' . strtotime(date('d M Y', $start_date) . ' 00:00:00'))
				->where('a.financialtrans_date < ' . strtotime(date('d M Y', $end_date) . ' 23:59:59'))
		;
		if ($coa_code != 'all') $select->where( ['d.coa_code' => $coa_code] );
		
		// echo $select->getSqlString(); exit();
		$list = $this->init('tblFinancialTrans')->selectWith($select);
		
		foreach ($list as $value) {
			$result[] = $value;
		}
		
		return $result;
	}
}
