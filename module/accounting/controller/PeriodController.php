<?php
namespace Accounting\Controller;

use Application\Controller\ParentController;
use Accounting\Model\Period;
use Administration\Model\UserLog;

class PeriodController extends ParentController
{
	public function indexAction()
	{
		$this->listAction();
	}
	
	public function listAction()
	{
		$this->isInRole('PERIOD');
		
		$this->printResponse('success', 'Selamat data di period', NULL);
	}
	
	public function ajaxupdateAction()
	{
		$userLog = new UserLog();
		$response = NULL;
		try {
			if ($this->isInRole('PERIOD')) {
				$period = new Period();
				$request = $this->getRequest();
				
				$year = $request->getPost('year');
				if (empty($year)) $this->printResponse('failed', 'Not found year !', ['reason' => 'Not found year !'] );
				
				$month_list = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
				$period_list = $period->getListByYear($year);
				
				if (!empty($period_list)) {
					foreach ($month_list as $month) {
						$period_begin = $year . $month;
						$is_already = $period->isAlready($period_begin);
						
						// jika tidak ada, tambah
						if ( !$is_already && (strlen($period_begin) == 6) ) $period->add( ['period_begin' => $period_begin] );
					}
				} else {
					foreach ($month_list as $month) {
						$period_begin = $year . $month;
						if (strlen($period_begin) == 6) $period->add( ['period_begin' => $period_begin] );
					}
				}
				
				$userLog->add($this->session->user_id, 'Update Period');
				$this->printResponse('success', 'period has update !', NULL);
			}
		} catch (Exception $e) {
			$userLog->add($this->session->user_id, 'Error try (period): '. $e->getMessage());
		}
	}
}
