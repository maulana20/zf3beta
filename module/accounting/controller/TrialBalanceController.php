<?php
namespace Accounting\Controller;

use Application\Controller\ParentController;
use Accounting\Model\TrialBalance;

class TrialBalanceController extends ParentController
{
	public function indexAction()
	{
		$this->listAction();
	}
	
	public function listAction()
	{
		$this->checkRole('ACCOUNTING');
		$this->printResponse('success', 'selamat datang di trial balance', 'selamat datang di trial balance');
	}
	
	public function searchAction()
	{
		$this->checkRole('GENERALLEDGER');
		
		$trialbalance = new TrialBalance();
		$request = $this->getRequest();
		
		$start_date = strtotime($request->getPost('start_date'));
		$end_date = strtotime($request->getPost('end_date'));
		
		$trialbalance_list = $trialbalance->getBeginSearch($start_date, $end_date);
		$content['list'] = $trialbalance_list;
		
		$this->printResponse('success', 'trial balance report search success', $content);

	}
}
