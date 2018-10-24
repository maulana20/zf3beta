<?php
namespace Administration\Controller;

use Application\Controller\ParentController;
use Administration\Model\User;
use Administration\Model\Group;

class AdminController extends ParentController
{
	public function indexAction()
	{
		$this->loginAction();
	}
	
	public function loginAction()
	{
		$user = new User();
		$group = new Group();
		$user_id = 1;
		
		$row = $user->getRow($user_id);
		$group_id = $row['group_id'];
		$this->session->user_id = $user_id;
		$this->session->user_name = $row['user_name'];
		
		$access = $group->getAccess($group_id);
		$access = unserialize($access);
		$this->setRole($access);
		
		return $this->redirect()->toRoute('user', ['action' => 'index']);
	}
	
	function logoutAction()
	{
		$user = new User();
		if (!empty($this->session->user_id)) {
			$user->updateLifeTime($this->session->user_id, time());
		}
		$this->destroyRole();
		echo 'anda sudah logout ganteng'; exit();
	}
}
