<?php
namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Model\PostRepository;
use Application\Model\Post;

class PostRepositoryFactory implements FactoryInterface
{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
	{
		$resultSetPrototype = new ResultSet();
		$resultSetPrototype->setArrayObjectPrototype(new PostRepository());

		$tableGateway = new TableGateway('posts', $container->get(AdapterInterface::class), null, $resultSetPrototype);
		return new Post($tableGateway);
	}
}
