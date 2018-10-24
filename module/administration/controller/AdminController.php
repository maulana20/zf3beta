<?php
namespace Administration\Controller;

use Application\Controller\ParentController;
use Administration\Model\User;
use Administration\Model\Group;
use Administration\Model\MenuBar;

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
		$menuBar = new MenuBar();
		$user_id = 1;
		
		$row = $user->getRow($user_id);
		$group_id = $row['group_id'];
		$this->session->user_id = $user_id;
		$this->session->user_name = $row['user_name'];
		
		$access = $group->getAccess($group_id);
		$access = unserialize($access);
		$this->setRole($access);
		
		$menu = $this->getAccessMenu($this->menu);
		$this->session->menu = $menuBar->MenuBar($menu);
		$user->update($user_id, array('user_login' => time(), 'login_attempt' => 0,));
		
		return $this->redirect()->toRoute('user', ['action' => 'index']);
	}
	
	public function logoutAction()
	{
		$user = new User();
		if (!empty($this->session->user_id)) {
			$user->updateLifeTime($this->session->user_id, time());
		}
		$this->destroyRole();
		echo 'anda sudah logout ganteng'; exit();
	}
	
	private function _getMenuCaption($access_menu)
	{
		$result = NULL;
		foreach ($access_menu as $k => $v) {
			if (is_array($v['node'])) {
				$i = -1;
				foreach ($v['node'] as $key => $val) {
					$i++;
					if ($val['node']) {
						// jadi array
						$j = -1;
						foreach ($val['node'] as $kkey => $vval) {
							$j++;
							$result[$v['caption']][$i][$val['caption']][$j] = $vval['caption'];
						}
					} else {
						$result[$v['caption']][$i] = $val['caption'];
					}
				}
			}
		}
		return $result;
	}
	
	public function getmenuAction()
	{
		$access_menu = $this->getAccessMenu($this->menu);
		$content['menu'] = $this->_getMenuCaption($access_menu);
		
		$this->printResponse('success', 'success getmenu', $content);
		exit();
	}
	
	function getuserloginAction()
	{
		$user = new UserModel();
		$email = $user->getEmail($this->session->user_id);
		$phone_list = $user->getPhone($this->session->user_id);
		$content['data']['user']['user_real_name'] = $this->session->user_trading;
		$content['data']['user']['user_email'] = $email;
		$content['data']['user']['phone_list'] = $phone_list;
		$content['data']['user']['user_deposit'] = $user->getDeposit($this->session->user_id);
		$is_su = $this->isInRole('SUPER_USER') ? 1 : 0;
		$is_su = $this->isInRole('SUPER_USER') ? 84 : 70;
		$content['data']['user']['8385'] = $is_su;
		$content['data']['user']['9080'] = $this->getzopimAction();
		$content['data']['user']['8066'] = $this->getpbAction();
		$content['data']['user']['8478'] = TRAVEL_NAME ? (int) TRAVEL_NAME : 1;
		$content['data']['user']['8476'] = TITLE_SITE ? TITLE_SITE : 'Atris';
		$content['data']['user']['8568'] = $this->isUnlimitedDownline();
		$content['data']['user']['6785'] = $this->iscanchangeupline();
		$content['data']['user']['url_login'] = URL_LOGIN;
		$content['data']['user']['url_logout'] = $this->session->url_logout;
		
		$response['message'] = Null;
		$response['status'] = 'success';
		$response['content'] = $content;
		echo Zend_Json::encode($response);
		exit();
	}
	
	function getinfoAction()
	{
		$content['data']['user']['8066'] = $this->getpbAction();
		$content['data']['user']['8478'] = TRAVEL_NAME ? (int) TRAVEL_NAME : 1;
		$content['data']['user']['8476'] = TITLE_SITE ? TITLE_SITE : 'Atris';
		$response['message'] = Null;
		$response['status'] = 'success';
		$response['content'] = $content;
		echo Zend_Json::encode($response);
		exit();
	}
	
	function getpbAction()
	{
		return 70;
	}
	
	function getzopimAction()
	{
		return 84;
	}
}
