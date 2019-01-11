<?php
namespace Administration\Controller;

use Application\Controller\ParentController;
use Administration\Model\User;
use Administration\Model\Group;
use Administration\Model\UserLog;

class UserController extends ParentController
{
	private function _ajaxexploitAction()
	{
		$userLog = new userLog();
		
		if (!empty($this->session->user_id)) $userLog->add($this->session->user_id, 'Exploit User');
		$this->destroyRole();
		
		$this->printResponse('failed', 'You get warning for exploit User !!!', 'You get warning for exploit User !!!');
	}
	
	private function _isDowner($down_role)
	{
		$result = true;
		$role = (!empty($down_role)) ? $down_role : array();
		foreach ($role as $val) {
			if (!$this->isInRole($val)) {
				$result = false;
				break;
			}
		}
		return $result;
	}
	
	public function indexAction()
	{
		$this->listAction();
	}
	
	public function listAction()
	{
		$user_id = $this->session->user_id;
		$this->checkRole('ADMINISTRATION');
		$this->checkRole('USER');
		
		$user = new User();
		$page = (int) $this->params()->fromQuery('page', 1);
		$page = ($page < 1) ? 1 : $page;
		$list = $user->getList($page, MAX_PAGE);
		$count_list = $user->getCount();
		$page_list = ceil($count_list / MAX_PAGE);
		$current_row = $user->getRow($user_id);
		
		$navigation = array();
		$navigation['in_inactive'] = true;
		$navigation['is_active'] = true;
		$navigation['is_delete'] = true;
		$navigation['is_deposit'] = false;
		$navigation['is_down'] = true;
		$navigation['is_up'] = true;
		
		$content = array();
		$content['page'] = $page;
		$content['navigation'] = $navigation;
		$content['ns_access_ai'] = 1;
		$content['current_row'] = $current_row;
		$content['distributor'] = $list[0]['group_name'];
		$content['caption'] = 'USER LIST';
		$content['page_list'] = $page_list;
		$content['list'] = $list;
		$content['canEdit'] = true;
		$content['UZ'] = 70;
		$content['user_id'] = $this->session->user_id;
		$content['back'] = array('controller' => 'home', 'action' => 'newsagent' , 'module' => 'default');
		
		$this->printResponse('success', 'user list data', $content);
	}
	
	public function addAction()
	{
		$this->checkpopRole('USER');
		
		$user = new User();
		$group = new Group();
		$request = $this->getRequest();
		
		$tempGroup1 = array();
		$tempGroup1 = $group->getList();
		foreach ($tempGroup1 as $row_group) {
			$unserialize = unserialize($row_group['group_access']);
			if ($this->_isDowner($unserialize)) {
				$tempGroup[] = $row_group;
			}
		}
		$group_list = array();
		if (!empty($tempGroup)) $group_list += $this->getLookupList($tempGroup, 'group_id', 'group_name');
		
		$content = array();
		$content['group_list'] = $group_list;
		$content['user_id'] = $this->session->user_id;
		$content['super_user'] = $this->isInRole('SUPER_USER');
		
		//if (!$request->isPost()) return $this->view->setTemplate('administration/user/add');
		$this->printResponse('success', '', $content);
	}
	
	public function ajaxaddAction() 
	{
		$userLog = new UserLog();
		try {
			$response = NULL;
			if ($this->isInRole('USER')) {
				$user = new User();
				$group = new Group();
				$request = $this->getRequest();
				
				$supersuper_useruest->getPost('group_id');
				$group_id = (int) $group_id;
				if (($group_id == 'NULL') || (empty($group_id))) $group_id = NULL;
				
				$password = $request->getPost('password');
				$confirm = $request->getPost('confirm');
				
				$str_to_replace = '_';
				$chr_not_valid = array('*', '?', '%');
				
				$user_row = $user->getRow($this->session->user_id);
				$name = $request->getPost('name');
				$name = trim(str_ireplace($chr_not_valid, $str_to_replace, preg_replace('/\s+/', ' ', $name) ));
				$name = ucwords(strtolower($name));
				
				$real_name = $request->getPost('real_name');
				$real_name = trim(str_ireplace($chr_not_valid, $str_to_replace, preg_replace('/\s+/', ' ', $real_name) ));
				$real_name = ucwords($real_name);
				
				$response['result'] = 'error';
				switch (true) {
					case (empty($real_name)) : $response['reason'] = 'Real name is empty !!! '; break;
					case (empty($name)) : $response['reason'] = 'Login name is empty !!! '; break;
					case ($user->isAlready($name)) : $response['reason'] = 'Login name already in database !!!  '; break;
					case (!$password) : $response['reason'] = 'Empty password not allowed !!! '; break;
					case (empty($group_id)) : $response['reason'] = 'Please choose Group !'; break;
					case ($password !== $confirm) : $response['reason'] = 'Password and confirm doesn\'t match !'; break;
					default : $response['result'] = 'ok';
				}
				if ($response['result'] == 'error') {
					$content['flag'] = 'alert';
					$content['alert'] = $response['reason'];
					$this->printResponse('failed', $response['reason'], $content);
				}
				
				$data = array();
				$data['user_realname'] = $real_name;
				$data['group_id'] = $group_id;
				$data['user_name'] = $name;
				$data['password'] = $password;
				$data['user_create_by'] = $this->session->user_id;
				
				if ($response['result'] == 'ok') {
					$user_id = $user->add($data);
					$userLog->add($this->session->user_id, 'Add User => ' . $name);
					$this->printResponse('success', 'add user success', NULL);
				}
			} else {
				$response = $this->reasonNoAccess();
			}
		} catch (Exception $e) {
			$userLog->add($this->session->user_id, 'Error try (add user): ' . $e->getMessage());
		}
		exit();
	}
	
	public function ajaxdeleteAction() 
	{
		$response = NULL;
		if ($this->isInRole('USER')) {
			$user = new User();
			$group = new Group();
			$userLog = new UserLog();
			$request = $this->getRequest();
			
			$allowed = true;
			if (!$allowed) $this->_ajaxexploitAction();
			
			$id = $request->getPost('id');
			
			if ($this->session->user_id != $id) {
				$row = $user->getRow($id);
				$row_group = $group->getRow($row['group_id']);
				$unserialize = unserialize($row_group['group_access']);
				if ($this->_isDowner($unserialize) && $allowed == true) {
					$user->delete($id);
					$userLog->add($this->session->user_id, 'Delete User => ' . $row['user_name']);
					$response['status'] = 'ok';
				} else {
					$response['status'] = 'error';
					$response['message'] = 'You can\'t delete user higher than your own !!!  ';
				}
			} else {
				$response['status'] = 'error';
				$response['message'] = 'You should not delete your own !!!  ';
			}
		} else {
			$response = $this->reasonNoAccess();
		}
		echo json_encode($response);
		exit();
	}
	
	public function editAction() 
	{
		$this->checkRole('USER');
		$user = new User();
		$group = new Group();
 		$request = $this->getRequest();
		
		$current_user_id = $this->session->user_id;
		$current_user_row = $user->getRow($current_user_id);
		$list = NULL;
		$group_list = array();
		$tempGroup = array();
		$tempGroup1 = $group->getList();
		foreach ($tempGroup1 as $row_group) {
			$unserialize = unserialize($row_group['group_access']);
			if ($this->_isDowner($unserialize)) {
				$tempGroup[] = $row_group;
			}
		}
		$group_access_list = $tempGroup;
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
			$content['reason'] = 'id kosong';
			$content['flag'] = 'alert';
			$this->printResponse('failed', $content['alert'], $content);
		}
		
		$allowed = true;
		if (!$allowed) {
			var_dump($allowed);exit();
			$this->_transfer('default', 'admin', 'nopopup');
			//$this->_ajaxexploitAction();
		}
		
		$list = $user->getRow($id);
		
		$row_group = $group->getRow($list['group_id']);
		$unserialize = (!empty($row_group['group_access'])) ? unserialize($row_group['group_access']) : NULL;
		$is_downer = true;
		if ($this->_isDowner($unserialize)) {
			if (!empty($group_access_list)) $group_list += $this->getLookupList($group_access_list, 'group_id', 'group_name');
			$content['group_list'] = $group_list;
		} else {
			$is_downer = false;
		}
		
		if ($is_downer && $allowed) {
		} else {
			$content['reason'] = 'This user group higher than yours, you can\'t edit it !!!';
			$content['flag'] = 'alert';
			$this->printResponse('failed', $content['reason'], $content);
		}
		$content['list'] = $list;
		
		$this->printResponse('success', 'user edit', $content);
	}
	
	function ajaxeditAction() 
	{
		$userLog = new UserLog();
		try {
			$response = NULL;
			if ($this->isInRole('USER')) {
				$group = new Group();
				$user = new User();
				$request = $this->getRequest();
				$data = array();
				$id = $request->getPost('id');
				
				$allowed = true;
				if (!$allowed) $this->_ajaxexploitAction();
				
				$group_id = $request->getPost('group_id');
				$group_id = (int) $group_id;
				if ( ($group_id == 'NULL') || (empty($group_id)) )  $group_id = NULL;
				
				$str_to_replace = '_';
				$chr_not_valid = array('*', '?', '%');
				$user_row = $user->getRow($this->session->user_id);
				
				$real_name = $request->getPost('real_name');
				$real_name = trim(str_ireplace($chr_not_valid, $str_to_replace, $real_name));
				$real_name = ucwords($real_name);
				
				$response['result'] = 'error';
				
				switch (true) {
					case ($id == $this->session->user_id) : $response['reason'] = 'Edit your own, not allowed !!!'; break;
					case (empty($real_name)) : $response['reason'] = 'Real name is empty !!! '; break;
					case (empty($group_id)) : $response['reason'] = 'Please choose Group !'; break;
					default : $response['result'] = 'ok';
				}
				
				if ($response['result'] == 'error') $this->printResponse('failed', $response['reason'], NULL);
				$data = array();
				$data['group_id'] = $group_id;
				$data['user_realname'] = $real_name;
				if (empty($data['group_id'])) {
					$response['result'] = 'error';
					$response['reason'] = 'Please choice Group !';
				} else {
					$access = $group->getAccess($data['group_id']);
					$access = unserialize($access);
					if (!($this->_isDowner($access))) {
						$response['result'] = 'error';
						$response['reason'] = 'The selected group doesn\'t meet requirements !!!';
					}
				}
				
				if ($response['result'] == 'error') $this->printResponse('failed', $response['reason'], NULL);
				if ($response['result'] == 'ok') {
					$user_id = $id;
					$user->update($user_id, $data);
					$list = $user->getRow($user_id);
					$userLog->add($this->session->user_id, 'Edit User => ' . $list['user_name']);
				} else {
					$response = $this->reasonNoAccess();
				}
				$this->printResponse('success', 'data has update !', NULL);
			}
		} catch (Exception $e) {
			$userLog->add($this->session->user_id, 'Error try (edit user): '. $e->getMessage());
		}
	}
	
	public function activeAction()
	{
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
	
	public function ajaxactiveAction()
	{
		$response = NULL;
		if ($this->isInRole('USER')) {
			$user = new User();
			$group = new Group();
			$userLog = new UserLog();
			$request = $this->getRequest();
			
			$id = $request->getPost('id');
			
			//$id = (int) $this->params()->fromRoute('id', 0);
			//if (!$id) return $this->redirect()->toRoute('user');
			
			$allowed = true;
			if (!$allowed) $this->_ajaxexploitAction();
			if ($this->session->user_id != $id) {
				$row = $user->getRow($id);
				$row_group = $group->getRow($row['group_id']);
				$unserialize = unserialize($row_group['group_access']);
				if ($this->_isDowner($unserialize)) {
					$user->active($id);
					$userLog->add($this->session->user_id, 'Inactive User => ' . $row['user_name']);
					$response['status'] = 'success';
				} else {
					$response['status'] = 'failed';
					$response['message'] = 'This user group higher than yours !!! ';
				}
			} else {
				$response['status'] = 'failed';
				$response['message'] = 'Can\'t inactive your own !!! ';
				$userLog->add($id, 'Inactive User => ' . $row['user_name']);
			}
		} else {
			$response = $this->reasonNoAccess();
		}
		
		//return $this->redirect()->toROute('user', ['action' => 'index', 'page' => $page]);
		echo json_encode($response);
		exit();
	}
	
	public function ajaxinactiveAction()
	{
		$response = NULL;
		if ($this->isInRole('USER')) {
			$user = new User();
			$group = new Group();
			$userLog = new UserLog();
			$request = $this->getRequest();
			
			$id = $request->getPost('id');
			
			//$id = (int) $this->params()->fromRoute('id', 0);
			//if (!$id) return $this->redirect()->toRoute('user');
			
			$allowed = true;
			if (!$allowed) $this->_ajaxexploitAction();
			if ($this->session->user_id != $id) {
				$row = $user->getRow($id);
				$row_group = $group->getRow($row['group_id']);
				$unserialize = unserialize($row_group['group_access']);
				if ($this->_isDowner($unserialize)) {
					$user->inActive($id);
					$userLog->add($this->session->user_id, 'Inactive User => ' . $row['user_name']);
					$response['status'] = 'success';
				} else {
					$response['status'] = 'failed';
					$response['message'] = 'This user group higher than yours !!! ';
				}
			} else {
				$response['status'] = 'failed';
				$response['message'] = 'Can\'t inactive your own !!! ';
				$userLog->add($id, 'Inactive User => ' . $row['user_name']);
			}
		} else {
			$response = $this->reasonNoAccess();
		}
		
		//return $this->redirect()->toROute('user', ['action' => 'index', 'page' => $page]);
		echo json_encode($response);
		exit();
	}
	
	function searchAction() 
	{
		$this->checkRole('USER');
		$user = new User();
		$request = $this->getRequest();
		$current_row = $user->getRow($this->session->user_id);
		$start_date = strtotime($request->getPost('start_date'));
		$create_start_date = strtotime($request->getPost('create_start_date'));
		$column_choice = $request->getPost('column_choice', 'user_name');
		$status_choice = $request->getPost('status_choice', 'any');
		$option = $request->getPost('option', 'per_pages');
		$search_txt = $request->getPost('search_txt');
		$search_txt = trim($search_txt);
		$partial = ($request->getPost('partial') == 'false') ? 0 : 1;
		
		if ((empty($search_txt)) && (substr($search_txt,0,1) !== '0')) {
			$search_txt2 = NULL;
		} else {
			$search = explode("\r", str_replace("\n","\r", $search_txt));
			foreach ($search as $val) {
				if (trim($val) != '') $txt[] = trim($val);
			}
			if (!empty($txt)) $search_txt2 = implode("\n", $txt);
			$in_sql = array('%', '_');
			$in_windowOS = array('*', '?');
			$search_txt = str_ireplace($in_windowOS, $in_sql, $search_txt2);
			$page = $page_list = NULL;
			if ($option == 'per_pages') {
				$countList = $user->getCountSearch($this->session->user_id, $search_txt, $column_choice, $partial, $create_start_date, $status_choice);
				$page_list = ceil($countList / MAX_PAGE);
				$page = (int) $this->params()->fromQuery('page', 1);
				if (($page > $page_list) && ($page_list > 0)) $page = $page_list;
				$content['page_list'] = $page_list;
			}
			$list = array();
			$list = $user->getListSearch($this->session->user_id, $search_txt, $column_choice, $partial, $create_start_date, $status_choice, $page, MAX_PAGE);
			if ($option != 'per_pages') $countList = count($list);
		}
		$content['page'] = ($option != 'per_pages') ? 1 : $page;
		$content['current_row'] = $current_row;
		$content['column_choice'] = $column_choice;
		$content['option'] = $option;
		$content['status_choice'] = $status_choice;
		$content['search_txt'] = $search_txt2;
		$content['partial'] = $partial;
		$content['start_date'] = date('d-m-Y', $start_date);
		$content['create_start_date'] = date('d-m-Y', $create_start_date);
		
		if (!$search_txt) { $content['caption'] = 'SEARCH . . .';
		} else if ($countList > 1) { $content['caption'] = 'Found ' . $countList . ' items';
		} else if (!empty($countList)) { $content['caption'] = 'Found ' . $countList . 'item';
		} else { $content['caption'] = 'There are no results to display.'; }
		
		$content['views'] = 'SearchUser';
		$content['list'] = $list;
		$content['UA'] = true;
		$content['UE'] = true;
		$content['UY'] = true;
		$content['UX'] = true;
		$content['UI'] = true;
		$content['super_user'] = true;
		$content['bottom'] = 'Bottom';
		$this->printResponse('success', 'Search user list', $content);
		exit();
	}
}
