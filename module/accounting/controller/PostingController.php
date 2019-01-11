<?php
namespace Accounting\Controller;

use Application\Controller\ParentController;
use Accounting\Model\Period;
use Accounting\Model\TrialBalance;
use Accounting\Model\Posting;
use Administration\Model\UserLog;

class PostingController extends ParentController
{
	public function indexAction()
	{
		$this->listAction();
	}
	
	public function listAction()
	{
		$this->checkRole('POSTING');
		
		$period = new Period();
		$year = $this->params()->fromQuery('year', date('Y', time()));
		$period_list = $period->getListByYear($year);
		
		$content['list'] = $period_list;
		
		$this->printResponse('success', 'Posting list success', $content);
	}
	
	public function ajaxpostingAction()
	{
		$userLog = new UserLog();
		$response = NULL;
		try {
			if ($this->isInRole('POSTING')) {
				$period = new Period();
				$request = $this->getRequest();
				
				$posting_checked = explode(' ', $request->getPost('posting'));
				$year = $request->getPost( 'year', date('Y', time()) );
				$period_list = $period->getListByYear($year);
				
				foreach ($period_list as $value) {
					if ($value['period_status'] != 'C') {
						if ( in_array($value['period_begin'], $posting_checked) ) {
							$status = 'P';
							
							//UPDATE POSTING 
							$trialbalance = new TrialBalance();
							$posting = new Posting();
							
							$date = substr($value['period_begin'], 0, 4) . '-' . substr($value['period_begin'], 4, 2) . '-01'; // 2018-10-01
							$date = strtotime($date);
							$start_date = strtotime(date('01 M Y', $date) . ' 00:00:00');
							$end_date = strtotime(date('t M Y', $date) . ' 23:59:59');
							
							$trialbalance_list = array();
							$trialbalance_list = $trialbalance->getBeginSearch($start_date, $end_date);
							if (empty($trialbalance_list)) $this->printResponse('failed', 'posting not update !', array('reason' => 'Gagal melakukan update posting !'));
							
							foreach ($trialbalance_list as $val) {
								if (empty($val['coa_id'])) break;
								$posting_id = $posting->getId($val['coa_id'], $value['period_begin']);
								usleep(10000);
								
								if (!empty($posting_id)) {
									$data = array();
									$data['posting_balance'] = !empty($val['ending']) ? $val['ending'] : 0;
									
									$posting->update($posting_id, $data);
								} else {
									$data = array();
									$data['coa_id'] = $val['coa_id'];
									$data['period_begin'] = $value['period_begin'];
									$data['posting_balance'] = !empty($val['ending']) ? $val['ending'] : 0;
									
									$posting->add($data);
								}
							}
						} else {
							$status = 'A';
						}
						$period->update( $value['period_begin'], ['period_status' => $status] );
					}
				}
				
				$userLog->add($this->session->user_id, 'Posting Period');
				$this->printResponse('success', 'posting has update !', NULL);
			}
		} catch (Exception $e) {
			$userLog->add($this->session->user_id, 'Error try (posting): '. $e->getMessage());
		}
	}
}
