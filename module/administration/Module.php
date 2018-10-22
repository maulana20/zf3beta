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
					'user' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/user[/:action]',
							'defaults' => [
								'controller' => Controller\UserController::class,
								'action'     => 'index',
							],
						],
					],
				],
			],
			'controllers' => [
				'factories' => [
					Controller\UserController::class => InvokableFactory::class,
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