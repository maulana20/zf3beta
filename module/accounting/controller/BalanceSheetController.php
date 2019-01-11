<?php
namespace Accounting\Controller;

use Application\Controller\ParentController;
use Accounting\Model\BalanceSheet;

class BalanceSheetController extends ParentController
{
	public function indexAction()
	{
		$this->listAction();
	}
	
	public function listAction()
	{
		$this->checkRole('ACCOUNTING');
		$this->checkRole('BALANCESHEET');
		
		$balancesheet = new BalanceSheet();
		$content['balancesheet_activa'] = $balancesheet->getCoaList('BS1');
		$content['balancesheet_passiva'] = $balancesheet->getCoaList('BS2');
		
		$this->printResponse('success', 'balance sheet data', $content);
	}
}
