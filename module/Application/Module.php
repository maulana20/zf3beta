<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

//use Zend\ModuleManager\ModuleManager;
//use Zend\Mvc\MvcEvent;

class Module implements ConfigProviderInterface
{
	const VERSION = '3.0.3-dev';
	
	/*public function init(ModuleManager $manager)
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
	}*/
	
	public function getConfig()
	{
		return include __DIR__ . '../config/module.config.php';
	}
	
	public function getServiceConfig()
	{
		return [
			'factories' => [
				Model\AlbumTable::class => function($container) {
					$tableGateway = $container->get(Model\AlbumTableGateway::class);
					return new Model\AlbumTable($tableGateway);
				},
				Model\AlbumTableGateway::class => function ($container) {
					$dbAdapter = $container->get(AdapterInterface::class);
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\Album());
					return new TableGateway('album', $dbAdapter, null, $resultSetPrototype);
				},
			],
		];
	}
	
	public function getControllerConfig()
	{
		return [
			'factories' => [
				Controller\AlbumController::class => function($container) {
					return new Controller\AlbumController(
						$container->get(Model\AlbumTable::class)
					);
				},
			],
		];
	}
}
