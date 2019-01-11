<?php
namespace Accounting\Controller;

use Application\Controller\ParentController;
use Accounting\Model\Period;
use Administration\Model\UserLog;

class ClosingController extends ParentController
{
	public function indexAction()
	{
		$this->listAction();
	}
	
	public function listAction()
	{
		$this->checkRole('CLOSING');
		
		$period = new Period();
		$year = $this->params()->fromQuery('year', date('Y', time()));
		$period_list = $period->getListByYear($year);
		
		$content['list'] = $period_list;
		
		$this->printResponse('success', 'Closing list success', $content);
	}
	
	public function ajaxclosingAction()
	{
		$userLog = new UserLog();
		$response = NULL;
		try {
			if ($this->isInRole('CLOSING')) {
				$period = new Period();
				$request = $this->getRequest();
				
				$closing_checked = explode(' ', $request->getPost('closing'));
				$year = $request->getPost( 'year', date('Y', time()) );
				$period_list = $period->getListByYear($year);
				
				foreach ($period_list as $value) {
					if ( in_array($value['period_status'], array('P', 'C')) ) {
						$status = ( in_array($value['period_begin'], $closing_checked) ) ? 'C' : 'A';
						$period->update( $value['period_begin'], ['period_status' => $status] );
					}
				}
				
				$userLog->add($this->session->user_id, 'Closing Period');
				$this->printResponse('success', 'closing has update !', NULL);
			}
		} catch (Exception $e) {
			$userLog->add($this->session->user_id, 'Error try (closing): '. $e->getMessage());
		}
	}
}
