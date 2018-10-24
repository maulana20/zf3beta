<?php
namespace Administration\Controller;

use Application\Controller\ParentController;
use Administration\Model\User;

class UserController extends ParentController
{
	public function indexAction()
	{
		$this->checkRole('ADMINISTRATION');
		$this->checkRole('USER');
		
		$user = new User();
		$page = (int) $this->params()->fromQuery('page', 1);
		$page = ($page < 1) ? 1 : $page;
		$user_list = $user->getList($page, MAX_PAGE);
		
		return new $this->view(['list' => $user_list]);
	}
	
	public function addAction()
	{
		$this->checkpopRole('ADD_USER');
		$this->checkpopRole('USER');
		
		$request = $this->getRequest();
		$user = new User();
		
		if (!$request->isPost()) return $this->view->setTemplate('administration/user/add');
		
		$data = array('artist' => $request->getPost('artist'), 'title' => $request->getPost('title'));
		$user->add($data);
		
		return $this->redirect()->toRoute('user', ['action' => 'index']);
	}
	
	public function deleteAction()
	{
		$this->checkRole('DELETE_USER');
		$this->checkRole('USER');
		$request = $this->getRequest();
		$user = new User();
		
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) return $this->redirect()->toRoute('user');
		
		if ($request->isPost()) {
			$del = $request->getPost('del', 'No');
			
			if ($del == 'Yes') {
				$id = $request->getPost('id');
				$user->delete($id);
			}
			
			return $this->redirect()->toRoute('user');
		}
		
		return [ 'id' => $id, 'user' => $user->get($id) ];
	}
	
	public function editAction()
	{
		$this->checkRole('EDIT_USER');
		$this->checkRole('USER');
		
		$request = $this->getRequest();
		$user = new User();
		
		$id = (int) $this->params()->fromRoute('id', 0);
		if (0 === $id) return $this->redirect()->toRoute('user', ['action' => 'add']);
		if (!$request->isPost()) return [ 'id' => $id, 'user' => $user->get($id) ];
		
		$data = array('artist' => $request->getPost('artist'), 'title' => $request->getPost('title'));
		$user->update($data, $id);
		
		return $this->redirect()->toROute('user', ['action' => 'index']);
	}
	
	public function activeAction()
	{
		$this->checkRole('AI_USER');
		$this->checkRole('USER');
		
		$request = $this->getRequest();
		$user = new User();
		
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) return $this->redirect()->toRoute('user');
		
		$data = array('user_status' => 'A');
		$user->update($id, $data);
		
		
		$page = (int) $this->params()->fromQuery('page', 1);
		$page += 1;
		
		return $this->redirect()->toROute('user', ['action' => 'index', 'page' => $page]);
	}
	
	public function inactiveAction()
	{
		$this->checkRole('AI_USER');
		$this->checkRole('USER');
		
		$request = $this->getRequest();
		$user = new User();
		
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) return $this->redirect()->toRoute('user');
		
		$data = array('user_status' => 'I');
		$user->update($id, $data);
		
		
		$page = (int) $this->params()->fromQuery('page', 1);
		$page += 1;
		
		return $this->redirect()->toROute('user', ['action' => 'index', 'page' => $page]);
	}
}
