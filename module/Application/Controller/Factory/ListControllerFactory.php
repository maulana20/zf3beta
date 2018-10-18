<?php
namespace Application\Controller\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Controller\ListController;
use Application\Model\PostRepositoryInterface;

class ListControllerFactory implements FactoryInterface
{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
	{
		return new ListController($container->get(PostRepositoryInterface::class));
	}
}
