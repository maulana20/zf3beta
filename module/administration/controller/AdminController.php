<?php
namespace Administration\Controller;

use Application\Controller\ParentController;
use Administration\Model\User;
use Administration\Model\Group;
use Administration\Model\MenuBar;
use Administration\Model\UserLog;
use Administration\Model\Rules;

class AdminController extends ParentController
{
	private function _forcelogin($user_id)
	{
		$user = new User();
		$userLog = new UserLog();
		
		$user_row = $user->getRow($user_id);
		if (empty($user_row['user_session'])) $this->printResponse('failed', 'Gagal mendapatkan session', 'Gagal mendapatkan session');
		$user->updateLifeTime($user_id, time());
		
		$userLog->add($this->session->user_id, 'force login');
		//$this->destroyRole();
		shell_exec('echo Y| DEL D:\\temp\\' . $user_row['user_session'] . ' /Q');
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
		$rules = new Rules();
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
		
		// CHECK FORCE LOGIN
		$user_explode = explode('/', $post['user']);
		$is_force = false;
		if (count($user_explode) > 1 ) {
			if ($user_explode[1] == 'force') $is_force = true;
			$post['user'] = $user_explode[0];
		}
		
		$user_id = $user->getId($post['user']);
		
		if ( $user->isUserPassword($post['user'], $post['password']) ) {
			if ($is_force) $this->_forcelogin($user_id);
			if ($user->isOnLogin($user_id)) {
				$userLog->add($user_id, $post['user'].' login inuse.');
				$this->session->user_id = $user_id;
				$http_referer = $_SERVER['HTTP_REFERER'];
				$domain_array_temp = explode( "/", $http_referer);
				$this->session->url_logout = $domain_array_temp[0] . "//" . $domain_array_temp[2];
				$this->printResponse('inuse', 'Your login name is inuse !!!', array('flag' => 'alert', 'alert' => 'nama login terpakai !!!'));
			} else {
				$temp = NULL;
				$row = $user->getRow($user_id);
				$group_id = $row['group_id'];
				if ($row['user_master'] > 0) {
					$this->session->downline = true;
				} else {
					$this->session->downline = false;
				}
				$this->session->user_id = $user_id;
				$this->session->user_name = $row['user_name'];
				$this->session->user_trading = $row['user_realname'];
				$this->session->group_id = $group_id;
				$this->session->user_email = NULL;
				$this->session->style = NULL;
				$this->session->password_attempt = 0;
				$rules_list = $rules->getList();
				/*foreach ($rules_list as $row_rules) {
					$temp[$row_rules['rules_code']]['rules_value'] = $row_rules['rules_value'];
					$temp[$row_rules['rules_code']]['rules_status'] = $row_rules['rules_status'];
				}*/
				
				$this->session->rules = $temp;
				$this->session->baseurl = $request->getPost('baseurl');
				$access = $group->getAccess($group_id);
				$access = unserialize($access);
				$this->setRole($access);
				$menu = $this->getAccessMenu($this->menu);
				$this->session->menu = $menuBar->MenuBar($menu);
				$user->update($user_id, array('user_login' => time(), 'login_attempt' => 0,));
				$user->updateLifeTime($this->session->user_id, time()+ EXPIRED);
				$user->update($user_id, array( 'user_session' => $this->getSessCookie() ) );
				
				$userLog->add($user_id, 'Log in');
				
				$http_referer = $_SERVER['HTTP_REFERER'];
				$domain_array_temp = explode( "/", $http_referer);
				$this->session->url_logout = $domain_array_temp[0] . "//" . $domain_array_temp[2];
				$this->printResponse('success', 'login success', 'login success');
			}
		//} else if ($user->isNoUserInDatabase()) {
		} else if ($user->isBlocked($post['user'])) {
			$agent_client = 'kami';
			$userLog->add($user_id, $post['user'] . ' Block user try login.');
			$this->printResponse('failed', 'Login ID anda terblokir, harap hubungi customer service ' . $agent_client . ' segera !!!', array('flag' => 'alert', 'alert' => 'Login ID anda terblokir, harap hubungi customer service'));
		} else if (!$user_id) {
			$userLog->add(NULL, $post['user'].' try login.');
			$this->printResponse('failed', 'Username atau Password Anda salah !!!', array('flag' => 'alert', 'alert' => 'Username atau Password Anda salah !!!'));
		} else {
			if (!empty($user_id)) {
				$userLog->add($user_id, 'Try Log in wrong password');
				$user->incPasswordAttempt($user_id);
			} else {
				$userLog->add(NULL, 'Unknown user try login.');
			}
			$this->printResponse('failed', 'Username or Password not match', array('flag' => 'alert', 'alert' => 'username atau password salah'));
		}
	}
	
	public function noaccessAction()
	{
		$this->printResponse('failed', 'SESSION TIMEOUT', array('flag'=>'alert', 'alert'=>'sesi habis'));
	}
	
	public function nopopupAction()
	{
		$this->printResponse('failed', 'SESSION TIMEOUT', array('flag'=>'alert', 'alert'=>'sesi habis'));
	}
	
	public function noactionAction()
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
	
	public function isonloginAction()
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
	
	public function getmenuAction()
	{
		$access_menu = $this->getAccessMenu($this->menu);
		$content['menu'] = $this->_getMenuCaption($access_menu);
		
		$this->printResponse('success', 'success getmenu', $content);
		exit();
	}
	
	public function getuserloginAction()
	{
		$user = new User();
		$content['data']['user']['user_real_name'] = $this->session->user_trading;
		$content['data']['user']['user_email'] = NULL;
		$content['data']['user']['phone_list'] = NULL;
		$content['data']['user']['user_deposit'] = 0;
		$content['data']['user']['8385'] = true;
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
	
	public function getinfoAction()
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
	
	public function getpbAction()
	{
		return 70;
	}
	
	public function getzopimAction()
	{
		return 84;
	}
	
	public function isUnlimitedDownline()
	{
		return 70;
	}
	
	public function iscanchangeupline()
	{
		return 70;
	}
}
