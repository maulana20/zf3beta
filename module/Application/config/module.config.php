<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Navigation;

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
            Controller\IndexController::class => InvokableFactory::class,
            //Controller\AlbumController::class => InvokableFactory::class
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => dirname(__DIR__) . '/src/view/layout/layout.phtml',
            'application/index/index' => dirname(__DIR__) . '/src/view/application/index/index.phtml',
            'error/404'               => dirname(__DIR__) . '/src/view/error/404.phtml',
            'error/index'             => dirname(__DIR__) . '/src/view/error/index.phtml',
        ],
        'template_path_stack' => [
            dirname(__DIR__) . '/src/view',
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
