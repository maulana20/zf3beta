<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

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
					'blog' => [
						'type'    => Segment::class,
						'options' => [
							'route' => '/blog[/:action[/:id]]',
							'constraints' => [
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'     => '[0-9]+',
							],
							'defaults' => [
								'controller' => Controller\ListController::class,
								'action'     => 'index',
							],
						],
					],
					'mahasiswa' => [
						'type'    => Segment::class,
						'options' => [
							'route' => '/mahasiswa[/:action[/:id]]',
							'constraints' => [
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'     => '[0-9]+',
							],
							'defaults' => [
								'controller' => Controller\MahasiswaController::class,
								'action'     => 'index',
							],
						],
					],
				],
			],
			'controllers' => [
				'factories' => [
					Controller\IndexController::class => InvokableFactory::class,
					Controller\AlbumController::class => Controller\Factory\AlbumControllerFactory::class,
					Controller\ListController::class => Controller\Factory\ListControllerFactory::class,
					Controller\MahasiswaController::class => InvokableFactory::class,
				],
			],
			'service_manager' => [
				/*'aliases' => [
					Model\PostRepositoryInterface::class => Model\PostRepository::class
				],*/
				'factories' => [
					Model\AlbumTable::class => Model\Factory\AlbumTableFactory::class,
					Model\PostsTable::class => Model\Factory\PostsTableFactory::class
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
					[
						'label' => 'Blog',
						'route' => 'blog',
					],
					[
						'label' => 'Mahasiswa',
						'route' => 'mahasiswa',
					],
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
