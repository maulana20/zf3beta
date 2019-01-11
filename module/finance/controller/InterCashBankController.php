<?php
namespace Finance\Controller;

use Application\Controller\ParentController;
use Finance\Model\InterCashBank;
use Accounting\Model\GeneralLedger;

class InterCashBankController extends ParentController
{
	public function indexAction()
	{
		$this->listAction();
	}
	
	public function listAction()
	{
		$this->checkRole('FINANCE');
		$this->checkRole('INTERCASHBANK');
		
		$intercashbank = new InterCashBank();

		$page = (int) $this->params()->fromQuery('page', 1);
		$page = ($page < 1) ? 1 : $page;
		$intercashbank_list = $intercashbank->getList($page, MAX_PAGE);
		$count_list = $intercashbank->getCount();
		$page_list = ceil($count_list / MAX_PAGE);
		
		$content['caption'] = 'INTER CASH BANK LIST';
		$content['list'] = $intercashbank_list;
		$content['page'] = $page;
		$content['page_list'] = $page_list;
		
		$this->printResponse('success', 'Inter Cash Bank list success', $content);
	}
	
	public function editAction()
	{
		$this->checkRole('INTERCASHBANK');
		
		$intercashbank = new InterCashBank();
		$generalledger = new GeneralLedger();
		
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) $this->printResponse( 'failed', 'Gagal mendapatkan id', ['flag' => 'alert', 'reason' => 'Gagal mendapatkan id'] );
		
		$list = $journal_list = array();
		
		$list = $intercashbank->getRow($id);
		if (!empty($list['financialtrans_out'])) $journal_list = $generalledger->getListSearchJournalByTrans( [$list['financialtrans_out'], $list['financialtrans_in']] );
		
		$content['list'] = $list;
		$content['journal_list'] = $journal_list;
		
		$this->printResponse('success', 'Inter Cash Bank edit', $content);
	}
}
