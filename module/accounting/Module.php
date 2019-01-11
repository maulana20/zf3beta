<?php
namespace Accounting;

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
					'groupaccount' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/groupaccount[/:action][/:id]',
							'defaults' => [
								'controller' => Controller\GroupAccountController::class,
								'action'     => 'index',
							],
						],
					],
					'coa' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/coa[/:action][/:id]',
							'defaults' => [
								'controller' => Controller\CoaController::class,
								'action'     => 'index',
							],
						],
					],
					'generalledger' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/generalledger[/:action][/:id]',
							'defaults' => [
								'controller' => Controller\GeneralLedgerController::class,
								'action'     => 'index',
							],
						],
					],
					'trialbalance' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/trialbalance[/:action][/:id]',
							'defaults' => [
								'controller' => Controller\TrialBalanceController::class,
								'action'     => 'index',
							],
						],
					],
					'balancesheet' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/balancesheet[/:action][/:id]',
							'defaults' => [
								'controller' => Controller\BalanceSheetController::class,
								'action'     => 'index',
							],
						],
					],
					'period' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/period[/:action][/:id]',
							'defaults' => [
								'controller' => Controller\PeriodController::class,
								'action'     => 'index',
							],
						],
					],
					'posting' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/posting[/:action][/:id]',
							'defaults' => [
								'controller' => Controller\PostingController::class,
								'action'     => 'index',
							],
						],
					],
					'closing' => [
						'type'    => Segment::class,
						'options' => [
							'route'    => '/closing[/:action][/:id]',
							'defaults' => [
								'controller' => Controller\ClosingController::class,
								'action'     => 'index',
							],
						],
					],
				],
			],
			'controllers' => [
				'factories' => [
					Controller\GroupAccountController::class => InvokableFactory::class,
					Controller\CoaController::class => InvokableFactory::class,
					Controller\GeneralLedgerController::class => InvokableFactory::class,
					Controller\TrialBalanceController::class => InvokableFactory::class,
					Controller\BalanceSheetController::class => InvokableFactory::class,
					Controller\PeriodController::class => InvokableFactory::class,
					Controller\PostingController::class => InvokableFactory::class,
					Controller\ClosingController::class => InvokableFactory::class,
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
