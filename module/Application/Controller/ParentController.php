<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

define('MAX_PAGE', 10);
define('EXPIRED', 900);
define('VERSION', '1.0');

class ParentController extends AbstractActionController
{
	public $session = NULL;
	public $view = NULL;
	
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
		$this->session = new Container('namespace');
		$this->view = new ViewModel();
	}
}
