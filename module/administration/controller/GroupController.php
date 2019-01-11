<?php
namespace Administration\Controller;

use Application\Controller\ParentController;
use Administration\Model\Group;
use Administration\Model\UserLog;

class GroupController extends ParentController
{
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
	
	private function _groupMenu()
	{
		$temp = $this->getAccessMenu($this->menu);
		$key = $node = array();
		foreach ($temp as $k => $v) {
			$key[$v['access']] = $k;
			if (!empty($v['node'])) {
				foreach ($v['node'] as $kk => $vv) {
					$node[$vv['access']] = $kk;
				}
			}
		}
		
		/*$temp[$key['ADMINISTRATION']]['node'][$node['USER']]['node'][] = array(
			'caption' => 'Add/Edit/Delete',
			'access' => 'EDIT_USER',
		);
		$temp[$key['ADMINISTRATION']]['node'][$node['USER']]['node'][] = array(
			'caption' => 'Reset Password',
			'access' => 'RESET_PASSWORD',
		);
		$temp[$key['ADMINISTRATION']]['node'][$node['GROUP']]['node'][] = array(
			'caption' => 'Add/Edit/Delete',
			'access' => 'EDIT_GROUP',
		);*/
		
		$menu = array();
		foreach ($temp as $key => $val) {
			if (!empty($val['node'])) {
				for ($i = 0; $i < count($val['node']); $i++) {
					if ($val['node'][$i]['access'] == 'SUPER_USER') $val['node'][$i]['access'] = '';
				}
			}
			$menu[] = $val;
		}
		
		return $menu;
	}
	
	public function indexAction()
	{
		$this->listAction();
	}
	
	public function listAction()
	{
		$this->checkRole('GROUP');
		$group = new Group();
		$request = $this->getRequest();
		
		$page = $count_list = NULL;
		//if ($this->isInRole('SUPER_USER')) {
			$page = (int) $this->params()->fromQuery('page', 1);
			$page = ($page < 1) ? 1 : $page;
			$list = $group->getList($page, MAX_PAGE);
			$count_list = $group->getCount();
			$page_list = ceil($count_list / MAX_PAGE);
		//}
		
		$content['caption'] = 'GROUP LIST (' . $count_list . ')';
		$content['page'] = $page;
		$content['page_list'] = $page_list;
		$content['views'] = 'Group';
		$content['list'] = $list;
		$content['super_user'] = true;
		$content['bottom'] = 'Bottom';
		$content['canEdit'] = true;
		$this->printResponse('success', 'Group list success', $content);
	}
	
	public function editAction() 
	{
		$this->checkRole('GROUP');
		
		$group = new Group();
		$request = $this->getRequest();
		
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) $this->printResponse( 'failed', 'Gagal mendapatkan id', ['flag' => 'alert', 'reason' => 'Gagal mendapatkan id'] );
		
		$list = $group->getRow($id);
		$list['group_access'] = unserialize($list['group_access']);
		$is_downer = $this->_isDowner($list['group_access']);
		if (!$is_downer) $this->printResponse( 'failed', 'This group higher than yours, you can\'t edit it !!!', ['flag' => 'alert', 'reason' => 'This group higher than yours, you can\'t edit it !!!'] );
		
		$menu = $this->_groupMenu();
		
		$content = array();
		$content['caption'] = 'EDIT GROUP';
		$content['group_id'] = $list['group_id'];
		$content['group_code'] = $list['group_code'];
		$content['group_name'] = $list['group_name'];
		$content['group_data'] = $list['group_access'];
		$content['group_menu'] = $menu;
		$content['super_user'] = true;
		$this->printResponse('success', 'Group edit', $content);
	}
	
	public function ajaxeditAction()
	{
		$response = NULL;
		if ($this->isInRole('GROUP')) {
			$data = NULL;
			$group = new Group();
			$userLog = new UserLog();
			$request = $this->getRequest();
			
			$id = $request->getPost('id');
			$row_group = $group->getRow($id);
			$old_code = (!empty($row_group['group_code'])) ? $row_group['group_code'] : NULL;
			$old_name = (!empty($row_group['group_name'])) ? $row_group['group_name'] : NULL;
			$old_access = NULL;
			if (!empty($row_group['group_access'])) $old_access = unserialize($row_group['group_access']);
			$str_to_replace = '_';
			$chr_not_valid = array('*', '?', '%');
			
			$code = $request->getPost('code');
			$code = trim(str_ireplace($chr_not_valid, $str_to_replace, $code));
			$code = strtoupper($code);
			
			if (empty($code)) {
				$this->printResponse('failed', 'Group code is empty !!! ', ['reason' => 'Group code is empty !!! ']);
			} else if (($old_code != $code) && ($group->isAlreadyCode($code))) {
				$this->printResponse('failed', 'Code already in database !!!  ', ['reason' => 'Code already in database !!!  ']);
			}
			
			$name = $request->getPost('name');
			$name = trim(str_ireplace($chr_not_valid, $str_to_replace, $name));
			$name = ucwords(strtolower($name));
			
			if (empty($name)) {
				$this->printResponse('failed', 'Group name is empty !!! ', ['reason' => 'Group name is empty !!! ']);
			} else if (($old_name != $name) && ($group->isAlready($name))) {
				$this->printResponse('failed', 'Name already in database !!!  ', ['reason' => 'Name already in database !!!  ']);
			} else if ($id == $this->session->group_id) {
				$this->printResponse('failed', 'Edit your own Group, not allowed !!!  ', ['reason' => 'Edit your own Group, not allowed !!!  ']);
			} else if (!$this->_isDowner($old_access)) {
				$userLog->add($this->session->user_id, 'Exploit Group');
				$this->printResponse('failed', 'You get warning, exploitation !!!', ['reason' => 'You get warning, exploitation !!!']);
			} else {
				$access = $request->getPost('access');
				if ($this->_isDowner($access)) {
					
					$data = array();
					$data['group_code'] = $code;
					$data['group_name'] = $name;
					$data['group_access'] = serialize(explode(' ', $access));
					$group->update($id, $data);
					
					if ($old_code != $code) {
						$userLog->add($this->session->user_id, 'Edit group => ' . $old_code . ' change to ' . $code);
					} if ($old_name != $name) {
						$userLog->add($this->session->user_id, 'Edit group => ' . $old_name . ' change to ' . $name);
					} else {
						$userLog->add($this->session->user_id, 'Edit group => ' . $old_name);
					}
					
					$this->printResponse('success', 'Group ajax edit success !', NULL);
				} else {
					$userLog->add($this->session->user_id, 'Exploit Group');
					$this->printResponse('failed', 'You get warning, exploitation !!!', ['reason' => 'You get warning, exploitation !!!']);
				}
			}
		} else {
			$this->reasonNoAccess();
		}
		$this->printResponse('error', $response, ['reason' => $response]);
	}
	
	function addAction()
	{
		$this->checkRole('GROUP');
		
		$group_data = array('ADMINITRATION');
		$menu = $this->_groupMenu();
		
		$content = array();
		$content['group_id'] = $content['group_name'] = NULL;
		$content['group_data'] = $group_data;
		$content['group_menu'] = $menu;
		$content['super_user'] = true;
		$this->printResponse('success', 'Group add', $content);
	}
	
	function ajaxaddAction()
	{
		$response = NULL;
		if ($this->isInRole('GROUP')) {
			$group = new Group();
			$userLog = new UserLog();
			$request = $this->getRequest();
			$data = NULL;
			$str_to_replace = '_';
			$chr_not_valid = array('*', '?', '%');
			
			$code = $request->getPost('code');
			$code = trim(str_ireplace($chr_not_valid, $str_to_replace, $code));
			$code = strtoupper($code);
			
			if (empty($code)) {
				$this->printResponse('failed', 'Group code is empty !!! ', ['reason' => 'Group code is empty !!! ']);
			} else if ($group->isAlreadyCode($code)) {
				$this->printResponse('failed', 'Code already in database !!!  ', ['reason' => 'Code already in database !!!  ']);
			}
			
			$name = $request->getPost('name');
			$name = trim(str_ireplace($chr_not_valid, $str_to_replace, $name));
			$name = ucwords(strtolower($name));
			
			if (empty($name)) {
				$this->printResponse('failed', 'Group name is empty !!! ', ['reason' => 'Group name is empty !!! ']);
			} else if ($group->isAlready($name)) {
				$this->printResponse('failed', 'Name already in database !!!  ', ['reason' => 'Name already in database !!!  ']);
			} else {
				$access = $request->getPost('access');
				if ($this->_isDowner($access)) {
					
					$data = array();
					$data['group_code'] = $code;
					$data['group_name'] = $name;
					$data['group_access'] = serialize(explode(' ', $access));
					$group->add($data);
					$userLog->add($this->session->user_id, 'Add Group => ' . $name);
					
					$this->printResponse('success', 'Group ajax add success !', NULL);
				} else {
					$userLog->add($this->session->user_id, 'Exploit Group');
					$this->printResponse('failed', 'You get warning, exploitation !!!', ['reason' => 'You get warning, exploitation !!!']);
				}
			}
		} else {
			$this->reasonNoAccess();
		}
		$this->printResponse('error', $response, ['reason' => $response]);
	}
	
	function ajaxdeleteAction()
	{
		$response = NULL;
		if ($this->isInRole('GROUP')) {
			$group = new Group();
			$userLog = new UserLog();
			$request = $this->getRequest();
			$id = $request->getPost('id');
			$row = $group->getRow($id);
			$access = unserialize($row['group_access']);
			if (!$id) {
				$this->printResponse('failed', 'Error !!!!! ', ['reason' => 'Error !!!!! ']);
			} else if (!$this->_isDowner($access)) {
				$this->printResponse('failed', 'This group higher than yours !!!', ['reason' => 'This group higher than yours !!!']);
			} else if ($this->session->group_id != $id) {
				if (!$group->isAnyUserInGroup($id)) {
					$group->delete($id);
					$userLog->add($this->session->user_id, 'Delete group => ' . $row['group_name']);
					$this->printResponse('success', 'Group ajax delete success !', NULL);
				} else {
					$this->printResponse('failed', "Can't delete this group !!! \nSome user use this group.", ['reason' => "Can't delete this group !!! \nSome user use this group."]);
				}
			} else {
				$this->printResponse('failed', 'You should not delete your own group !!!  ', ['reason' => 'You should not delete your own group !!!  ']);
			}
		} else {
			$this->reasonNoAccess();
		}
		$this->printResponse('error', $response, ['reason' => $response]);
	}
}
