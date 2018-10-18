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

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Navigation;

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
		return [
			'router' => [
				'routes' => [
					'home' => [
						'type' => Literal::class,
						'options' => [
							'route'    => '/',
							'defaults' => [
								'controller' => Controller\IndexController::class,
								'action'     => 'index',
							],
						],
					],
					'application' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/application[/:action]',
							'defaults' => [
								'controller' => Controller\IndexController::class,
								'action'     => 'index',
							],
						],
					],
					'album' => [
						'type'    => Segment::class,
						'options' => [
							'route' => '/album[/:action[/:id]]',
							'constraints' => [
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'     => '[0-9]+',
							],
							'defaults' => [
								'controller' => Controller\AlbumController::class,
								'action'     => 'index',
							],
						],
					],
				],
			],
			'controllers' => [
				'factories' => [
					Controller\IndexController::class => InvokableFactory::class
				],
			],
			'view_manager' => [
				'display_not_found_reason' => true,
				'display_exceptions'       => true,
				'doctype'                  => 'HTML5',
				'not_found_template'       => 'error/404',
				'exception_template'       => 'error/index',
				'template_map' => [
					'layout/layout'           => dirname(__DIR__) . DIRECTORY_SEPARATOR . __NAMESPACE__ . '/view/layout/layout.phtml',
					'application/index/index' => dirname(__DIR__) . DIRECTORY_SEPARATOR . __NAMESPACE__ . '/view/application/index/index.phtml',
					'error/404'               => dirname(__DIR__) . DIRECTORY_SEPARATOR . __NAMESPACE__ . '/view/error/404.phtml',
					'error/index'             => dirname(__DIR__) . DIRECTORY_SEPARATOR . __NAMESPACE__ . '/view/error/index.phtml',
				],
				'template_path_stack' => [
					dirname(__DIR__) . DIRECTORY_SEPARATOR . __NAMESPACE__ . '/view',
				],
			],
			'navigation' => [
				'default' => [
					[
						'label' => 'Home',
						'route' => 'home',
					],
					[
						'label' => 'Album',
						'route' => 'album',
						'pages' => [
							[
								'label' => 'Add',
								'route' => 'album',
								'action' => 'add',
							],
							[
								'label' => 'Edit',
								'route' => 'album',
								'action' => 'edit',
							],
							[
								'label' => 'Delete',
								'route' => 'album',
								'action' => 'delete',
							],
						],
					],
				],
			],
		];
	}
	
	public function getServiceConfig()
	{
		return [
			'factories' => [
				Model\AlbumTable::class => Model\Factory\AlbumTableFactory::class
			],
		];
	}
	
	public function getControllerConfig()
	{
		return [
			'factories' => [
				Controller\AlbumController::class => Controller\Factory\AlbumControllerFactory::class
			],
		];
	}
}
