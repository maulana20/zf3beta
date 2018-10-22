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
		$user_list = $user->getList($page, MAX_PAGE);
		
		return new $this->view(['list' => $user_list]);
	}
	
	public function addAction()
	{
		$request = $this->getRequest();
		
		if (!$request->isPost()) return $this->view->setTemplate('administration/user/add');;
		
		$user = new User();
		$data = array('artist' => $request->getPost('artist'), 'title' => $request->getPost('title'));
		$user->add($data);
        return $this->redirect()->toRoute('user', ['action' => 'index']);
	}
}
