<?php
namespace Administration;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

class Module implements ConfigProviderInterface
{
	const VERSION = '3.0.3-dev';
	
	public function getConfig()
	{
		return [
			'router' => [
				'routes' => [
					'admin' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/admin[/:action][/:id]',
							'defaults' => [
								'controller' => Controller\AdminController::class,
								'action'     => 'index',
							],
						],
					],
					'user' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/user[/:action][/:id]',
							'defaults' => [
								'controller' => Controller\UserController::class,
								'action'     => 'index',
							],
						],
					],
					'group' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/group[/:action][/:id]',
							'defaults' => [
								'controller' => Controller\GroupController::class,
								'action'     => 'index',
							],
						],
					],
					'userlog' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/userlog[/:action][/:id]',
							'defaults' => [
								'controller' => Controller\UserLogController::class,
								'action'     => 'index',
							],
						],
					],
				],
			],
			'controllers' => [
				'factories' => [
					Controller\AdminController::class => InvokableFactory::class,
					Controller\UserController::class => InvokableFactory::class,
					Controller\GroupController::class => InvokableFactory::class,
					Controller\UserLogController::class => InvokableFactory::class,
				],
			],
			'service_manager' => [
				'aliases' => [
				],
				'factories' => [
				],
			],
			'view_manager' => [
				'template_path_stack' => [
					dirname(__DIR__) . DIRECTORY_SEPARATOR . __NAMESPACE__ . '/view',
				],
			],
			'db' => [
				'host'		=> '',
				'driver'	=> 'Pdo',
				'dsn'		=> sprintf('sqlite:%s/data/zftutorial.db', realpath(getcwd())),
				'database'	=> '',
				'username'	=> '',
				'password'	=> '',
			],
		];
	}
}
