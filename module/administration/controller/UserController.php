<?php
namespace Administration\Controller;

use Application\Controller\ParentController;
use Administration\Model\User;

class UserController extends ParentController
{
	public function indexAction()
	{
		$user = new User();
		$page = (int) $this->params()->fromQuery('page', 1);
		$page = ($page < 1) ? 1 : $page;
		$user_list = $user->getList();
		foreach ($user_list as $value) {
			var_dump(json_encode($value));
		}
	}
}
