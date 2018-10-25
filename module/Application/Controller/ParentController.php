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

define('MAX_PAGE', 10);
define('EXPIRED', 900);
define('VERSION', '1.0');

define('AGENT_CLIENT', 'demo');
define('URL_LOGIN', 'http://atris.vpstiket.com');
define('TRAVEL_NAME', 98);
define('TITLE_SITE', 'VAS');
define('DOMAIN_AVAILABLE', 'http://client.vpstiket.com~http://m-atris.vpstiket.com');

class ParentController extends AbstractActionController
{
	public $session = NULL;
	public $view = NULL;
	public $menu = array(
		array('caption' => 'Administration', 'href' => '#', 'access' => 'ADMINISTRATION', 'node' => array(
				array('caption' => 'User List', 'href' => '#', 'onclick' => '', 'access' => 'USER'),
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
			if ($this->getEvent()->getRouteMatch()->getMatchedRouteName() != 'admin') {
				$user->updateLifeTime($this->session->user_id, time()+ EXPIRED);
			}
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
			echo 'gak ada access check role woyy !!'; exit();
			//$this->_transfer('default', 'admin', 'noaccess');
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
	
	function getSessCookie()
	{
		$http_cookie = stristr($_SERVER['HTTP_COOKIE'], 'PHPSESSID');
		$http_cookie_exp = explode('=', $http_cookie);
		if (empty($http_cookie_exp[1])) return NULL;
		return 'sess_' . $http_cookie_exp[1];
	}
}
