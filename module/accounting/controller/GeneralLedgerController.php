<?php
namespace Accounting\Controller;

use Application\Controller\ParentController;
use Accounting\Model\GeneralLedger;

class GeneralLedgerController extends ParentController
{
	public function indexAction()
	{
		$this->listAction();
	}
	
	public function listAction()
	{
		$this->checkRole('ACCOUNTING');
		$this->printResponse('success', 'selamat datang di generalledger', 'selamat datang di generalledger');
	}
	
	public function searchjournalAction()
	{
		$this->checkRole('JOURNAL');
		
		$generalledger = new GeneralLedger();
		$request = $this->getRequest();
		
		$start_date = strtotime($request->getPost('start_date'));
		$end_date = strtotime($request->getPost('end_date'));
		$journal_list = $generalledger->getListSearchJournal($start_date, $end_date);
		$content['list'] = $journal_list;
		
		$this->printResponse('success', 'journal search success', $content);
	}
	
	public function searchreportAction()
	{
		$this->checkRole('GENERALLEDGER');
		
		$generalledger = new GeneralLedger();
		$request = $this->getRequest();
		
		$start_date = strtotime($request->getPost('start_date'));
		$end_date = strtotime($request->getPost('end_date'));
		$coa_code = $request->getPost('coa_code');
		
		$generalledger_list = $generalledger->getListSearchReport($start_date, $end_date, $coa_code);
		$content['list'] = $generalledger_list;
		
		$this->printResponse('success', 'generalledger report search success', $content);
	}
}
