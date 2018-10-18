<?php

namespace Application\Model\Factory;

use Application\Model\Album;
use Application\Model\AlbumTable;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;

class AlbumTableFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Album());

        $tableGateway = new TableGateway('album', $container->get(AdapterInterface::class), null, $resultSetPrototype);
        return new AlbumTable($tableGateway);
    }
}
