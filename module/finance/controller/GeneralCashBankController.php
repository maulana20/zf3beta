<?php
namespace Finance\Controller;

use Application\Controller\ParentController;
use Finance\Model\GeneralCashBank;
use Accounting\Model\GeneralLedger;

class GeneralCashBankController extends ParentController
{
	public function indexAction()
	{
		$this->listAction();
	}
	
	public function listAction()
	{
		$this->checkRole('FINANCE');
		$this->checkRole('GENERALCASHBANK');
		
		$generalcashbank = new GeneralCashBank();

		$page = (int) $this->params()->fromQuery('page', 1);
		$page = ($page < 1) ? 1 : $page;
		$generalcashbank_list = $generalcashbank->getList($page, MAX_PAGE);
		$count_list = $generalcashbank->getCount();
		$page_list = ceil($count_list / MAX_PAGE);
		
		$content['caption'] = 'GENERAL CASH BANK LIST';
		$content['list'] = $generalcashbank_list;
		$content['page'] = $page;
		$content['page_list'] = $page_list;
		$this->printResponse('success', 'General Cash Bank list success', $content);
	}
	
	public function editAction()
	{
		$this->checkRole('FINANCE');
		
		$generalcashbank = new GeneralCashBank();
		$generalledger = new GeneralLedger();
		
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) $this->printResponse( 'failed', 'Gagal mendapatkan id', ['flag' => 'alert', 'reason' => 'Gagal mendapatkan id'] );
		
		$list = $journal_list = array();
		$list = $generalcashbank->getRow($id);
		if (!empty($list['financialtrans_id']) && !empty($list['coa_id'])) $journal_list = $generalledger->getListSearchJournalByTrans( [$list['financialtrans_id']], $list['coa_id'] );
		
		$content['list'] = $list;
		$content['journal_list'] = $journal_list;
		
		$this->printResponse('success', 'General Cash Bank edit', $content);
	}
}
