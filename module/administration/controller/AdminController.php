<?php
namespace Administration\Controller;

use Application\Controller\ParentController;
use Administration\Model\User;
use Administration\Model\Group;
use Administration\Model\MenuBar;
use Administration\Model\UserLog;

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
		$userLog = new userLog();
		$request = $this->getRequest();
		
		if ((!empty($this->session->temp_username)) or (!empty($this->session->temp_password))) {
			$post = array(
				'user' => $this->session->temp_username,
				'password' => $this->session->temp_password,
			);
			unset($this->session->temp_userneme);
			unset($this->session->temp_password);
		} else {
			$post = array(
				'user' => $request->getPost('user'),
				'password' => $request->getPost('password'),
			);
		}
		
		$user_id = $user->getId($post['user']);
		
		if ( $user->isUserPassword($post['user'], $post['password']) ) {
		//} else if ($user->isNoUserInDatabase()) {
		} else if ($user->isBlocked($post['user'])) {
			$agent_client = 'kami';
			$userLog->add($user_id, $post['user'] . ' Block user try login.');
			$this->printResponse('failed', 'Login ID anda terblokir, harap hubungi customer service ' . $agent_client . ' segera !!!', array('flag'=>'alert', 'alert'=>'Login ID anda terblokir, harap hubungi customer service'));
		} else if (!$user_id) {
		} else {
		}
		
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
	
	function ajaxresetloginAction()
	{
		$user = new User();
		//$userLog = new userLogModel();
		$request = $this->getRequest();
		
		$is_agree = $request->getPost('is_agree', 0);
		if (!$is_agree) {
			$this->printResponse('failed', 'Silahkkan ceklist Setuju', 'Silahkkan ceklist Setuju');
			exit();
		}
		$user_row = $user->getRow($this->session->user_id);
		//$this->session->user_name = $user_row['user_name'];
		if (empty($user_row['user_session'])) {
			$this->printResponse('failed', 'Gagal mendapatkan session', 'Gagal mendapatkan session');
			exit();
		}
		// UPDATE LIFETIME
		if (! empty ( $this->session->user_id )) {
			$user->updateLifeTime($this->session->user_id, time());
			//$userLog->add($this->session->user_id, 'Log out');
			//delete token
			if ((DATABASE == 'klikmbc') || (DATABASE == 'demox')) {
				if ($this->session->tokenExpired == 'onetime') {
					$token = new TokenModel();
					$token->delete($this->session->tokenId);
				}
			}
		}
		// DELETE SESSION PADA BROWSER SENDIRI
		$userLog->add($this->session->user_id, 'force login');
		$this->destroyRole();
		// DELETE COOKIE PADA TEMP SESSION
		shell_exec('echo Y| DEL D:\\temp\\' . $user_row['user_session'] . ' /Q');
		//sleep(2);
		
		$this->printResponse('success', 'Login berhasil', 'Login berhasil');
		exit();
	}
	
	function noaccessAction()
	{
		$this->printResponse('failed', 'SESSION TIMEOUT', array('flag'=>'alert', 'alert'=>'sesi habis'));
	}
	
	function nopopupAction()
	{
		$this->printResponse('failed', 'SESSION TIMEOUT', array('flag'=>'alert', 'alert'=>'sesi habis'));
	}
	
	function noactionAction()
	{
		$this->printResponse('failed', 'THIS PAGE UNDER CONSTRUCTION !!!', array('flag'=>'alert', 'alert'=>'halaman sedang dibuat'));
	}
	
	public function logoutAction()
	{
		$user = new User();
		$userLog = new userLog();
		if (!empty($this->session->user_id)) {
			$user->updateLifeTime($this->session->user_id, time());
			$userLog->add($this->session->user_id, 'Log out');
		}
		$this->destroyRole();
		echo 'anda sudah logout ganteng'; exit();
	}
	
	function isonloginAction()
	{
		$user = new User();
		$response['status'] = 'failed';
		$response['message'] = 'Gagal Login';
		$response['content'] = array('info' => NULL, 'url_login' => URL_LOGIN, 'url_logout' => $this->session->url_logout);
		
		if($this->session->user_name) {
			$response['status'] = 'success';
			$response['message'] = 'Berhasil login';
			$response['content'] = array('info' => 'Berhasil', 'url_login' => URL_LOGIN, 'url_logout' => $this->session->url_logout);
		}
		echo json_encode($response);
		exit();
	}
	
	private function _getMenuCaption($access_menu)
	{
		$result = NULL;
		foreach ($access_menu as $k => $v) {
			$node = (!empty($v['node'])) ? $v['node'] : NULL;
			if (is_array($node)) {
				$i = -1;
				foreach ($v['node'] as $key => $val) {
					$i++;
					$_node = (!empty($val['node'])) ? $val['node'] : NULL;
					if ($_node) {
						// jadi array
						$j = -1;
						foreach ($_node as $kkey => $vval) {
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
		$user = new User();
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
		echo json_encode($response);
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
		echo json_encode($response);
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
	
	function isUnlimitedDownline()
	{
		return 70;
	}
	
	function iscanchangeupline()
	{
		return 70;
	}
}
