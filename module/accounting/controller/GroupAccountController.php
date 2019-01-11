<?php
namespace Accounting\Controller;

use Application\Controller\ParentController;
use Accounting\Model\GroupAccount;

class GroupAccountController extends ParentController
{
	public function indexAction()
	{
		$this->listAction();
	}
	
	public function listAction()
	{
		$this->checkRole('ACCOUNTING');
		$this->checkRole('COA');
		
		$groupaccount = new GroupAccount();
		
		$groupaccount_list = $groupaccount->getList();
		$content['list'] = $groupaccount_list;
		
		$this->printResponse('success', 'groupaccount list success', $content);
	}
}
