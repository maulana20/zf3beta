<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\Permissions\Acl\Acl;;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Administration\Model\Group;
use Administration\Model\User;

define('MAX_PAGE', 25);
define('EXPIRED', 900);
define('VERSION', '1.0');

define('AGENT_CLIENT', 'demo');
define('URL_LOGIN', 'http://localhost:8080');
define('TRAVEL_NAME', 1);
define('TITLE_SITE', 'VAS');
define('DOMAIN_AVAILABLE', 'http://localhost:8080~http://http://m-localhost:8080');
date_default_timezone_set("Asia/Bangkok");

class ParentController extends AbstractActionController
{
	public $session = NULL;
	public $view = NULL;
	public $menu = array(
		array('caption' => 'Administration', 'href' => '#', 'access' => 'ADMINISTRATION', 'node' => array(
				array('caption' => 'User', 'href' => '#', 'onclick' => '', 'access' => 'USER'),
				array('caption' => 'Group', 'href' => '#', 'onclick' => '', 'access' => 'GROUP'),
				array('caption' => 'Show User Log', 'href' => '#', 'onclick' => '', 'access' => 'USER_LOG'),
			),
		),
		array('caption' => 'Operational', 'href' => '#', 'access' => 'OPERATIONAL'),
		array('caption' => 'Accounting', 'href' => '#', 'access' => 'ACCOUNTING', 'node' => array(
				array('caption' => 'COA', 'href' => '#', 'onclick' => '', 'access' => 'COA'),
				array('caption' => 'Journal', 'href' => '#', 'onclick' => '', 'access' => 'JOURNAL'),
				array('caption' => 'General Ledger', 'href' => '#', 'onclick' => '', 'access' => 'GENERALLEDGER'),
				array('caption' => 'Trial Balance', 'href' => '#', 'onclick' => '', 'access' => 'TRIALBALANCE'),
				array('caption' => 'Balance Sheet', 'href' => '#', 'onclick' => '', 'access' => 'BALANCESHEET'),
				array('caption' => 'Period', 'href' => '#', 'onclick' => '', 'access' => 'PERIOD'),
				array('caption' => 'Posting', 'href' => '#', 'onclick' => '', 'access' => 'POSTING'),
				array('caption' => 'Closing', 'href' => '#', 'onclick' => '', 'access' => 'CLOSING'),
			),
		),
		array('caption' => 'Finance', 'href' => '#', 'access' => 'FINANCE', 'node' => array(
				array('caption' => 'General Cash Bank', 'href' => '#', 'onclick' => '', 'access' => 'GENERALCASHBANK'),
				array('caption' => 'Inter Cash Bank', 'href' => '#', 'onclick' => '', 'access' => 'INTERCASHBANK'),
			),
		),
	);
	
	function getAccessMenu($menu)
	{
		$result = NULL;
		if (!empty($menu)) {
			$j = 0;
			$count = count($menu);
			for ($i = 0; $i < $count; $i++) {
				if ((!empty($menu[$i]['access'])) && (!$this->isInRole($menu[$i]['access']))) continue;
				$result[$j] = $menu[$i];
				if (!empty($menu[$i]['node'])) {
					$access = $this->getAccessMenu($menu[$i]['node']);
					if (!empty($access)) {
						$result[$j]['node'] = $access;
					} else {
						unset($result[$j]['node']);
					}
				}
				$j++;
			}
		}
		
		return $result;
	}
	
	public function init(ModuleManager $manager)
	{
		$eventManager = $manager->getEventManager();
		$sharedEventManager = $eventManager->getSharedManager();
		$sharedEventManager->attach(__NAMESPACE__, 'dispatch', [$this, 'onDispatch'], 100);
	}
	
	public function onDispatch(MvcEvent $event)
	{
		$controller = $event->getTarget();
		$controllerClass = get_class($controller);
		$moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
		
		if ($moduleNamespace == __NAMESPACE__) {
			$viewModel = $event->getViewModel();
			$viewModel->setTemplate('layout/layout');
		}
		$this->setUp();
		AbstractActionController::onDispatch($event);
	}
	
	public function setUp()
	{
try {
		$this->view = new ViewModel();
		$this->session = new Container('namespace');
		
		if ($this->session->user_id == 1) {
			$this->session->setExpirationSeconds(1800);
		} else {
			$this->session->setExpirationSeconds(EXPIRED);
		}
		
		if (!isset($this->session->acl)) {
			$group = new Group();
			$acl = new Acl();
			$access_all = $group->getAccessAll();
			foreach ($access_all as $a) {
				$acl->addRole(new Role($a));
			}
			$this->session->acl = serialize($acl);
		} else {
			$user = new User();
			if ($this->getEvent()->getRouteMatch()->getMatchedRouteName() != 'admin') $user->updateLifeTime($this->session->user_id, time()+ EXPIRED);
			if ( !empty($this->session->user_name) ) $user->update($this->session->user_id, array( 'user_session' => $this->getSessCookie() ) );
		}
} catch (Exception $e) {
	echo $e->getMessage();
	exit();
}
	}
	
	public function checkRole($role)
	{
		if (!$this->isInRole($role)) {
			if (!empty($this->session->user_id)) {
				$user = new User();
				$user->updateLifeTime($this->session->user_id, time());
			}
			$this->destroyRole();
			$this->printResponse('timeout', 'failed checkpopRole ', array('flag'=>'timeout', 'alert'=>'Anda tidak memiliki access, harap hubungi vendor anda !'));
			exit();
		}
	}
	
	public function checkpopRole($role)
	{
		if (!$this->isInRole($role)) {
			if (!empty($this->session->user_id)) {
				$user = new User();
				$user->updateLifeTime($this->session->user_id, time());
			}
			$this->destroyRole();
			echo 'gak ada access check pop role woyy !!'; exit();
			//$this->_transfer('default', 'admin', 'nopopup');
		}
	}
	
	public function isInRole($role)
	{
		$acl = unserialize($this->session->acl);
		return ($acl->isAllowed($role));
	}
	
	public function setRole($allow)
	{
		$group = new Group();
		$acl = new Acl();
		$access_all = $group->getAccessAll();
		
		foreach ($access_all as $a) {
			$acl->addRole(new Role($a));
		}
		foreach ($allow as $a) {
			$acl->allow($a);
		}
		
		$this->session->acl = serialize($acl);
	}
	
	public function destroyRole()
	{
		$this->session->getManager()->destroy();
		//Zend_Session::expireSessionCookie();
	}
	
	public function printResponse($status, $message = NULL, $content = NULL)
	{
		$response = NULL;
		$response['status'] = $status;
		$response['message'] = $message;
		$response['content'] = $content;
		
		echo json_encode($response);
		exit();
	}
	
	public function reasonNoAccess()
	{
		$this->destroyRole();
		$this->printResponse('failed', 'not have access', array('flag'=>'alert', 'alert'=>'Anda tidak memiliki access , harap hubungi vendor anda !'));
	}
	
	public function NoAccessAllowed()
	{
		$result = array(
			'result' => 'error',
			'reason' => 'Error Code 25, Please contact Vendor!',
		);
		return $result;
	}
	
	public function getLookupList($list, $key, $val)
	{
		$result = NULL;
		$count = count($list);
		for($i = 0; $i < $count; $i++) {
			$result[$list[$i][$key]] = $list[$i][$val];
		}
		return $result;
	}
	
	function getSessCookie()
	{
		$http_cookie = stristr($_SERVER['HTTP_COOKIE'], 'PHPSESSID');
		$http_cookie_exp = explode('=', $http_cookie);
		$session_cookie = 'sess_';
		if (empty($http_cookie_exp[1])) return NULL;
		if (stristr($http_cookie_exp[1], ';')) {
			$cookie_temp = explode(';', $http_cookie);
			return $session_cookie .= $cookie_temp[0];
		} else {
			return $session_cookie .= $http_cookie_exp[1];
		}
	}
}
