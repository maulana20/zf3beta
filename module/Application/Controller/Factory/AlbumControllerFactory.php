<?php 
namespace Application\Controller\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Controller\AlbumController;
use Application\Model\AlbumTable;

class AlbumControllerFactory implements FactoryInterface
{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) 
	{
		return new AlbumController($container->get(AlbumTable::class));
	}
}
