<?php
namespace Zend\Db\TableGateway;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql;
use Zend\Db\Sql\TableIdentifier;

class TableGateway extends AbstractTableGateway
{
	public $lastInsertValue;
	public $table;
	public $adapter;

	public function __construct(
		string|TableIdentifier $table,
		AdapterInterface $adapter,
		Feature\AbstractFeature|Feature\FeatureSet|Feature\AbstractFeature[] $features = null,
		ResultSetInterface $resultSetPrototype = null,
		Sql\Sql $sql = null
	);

	/** Inherited from AbstractTableGateway */

	public function isInitialized() : bool;
	public function initialize() : void;
	public function getTable() : string;
	public function getAdapter() : AdapterInterface;
	public function getColumns() : array;
	public function getFeatureSet() Feature\FeatureSet;
	public function getResultSetPrototype() : ResultSetInterface;
	public function getSql() | Sql\Sql;
	public function select(Sql\Where|callable|string|array $where = null) : ResultSetInterface;
	public function selectWith(Sql\Select $select) : ResultSetInterface;
	public function insert(array $set) : int;
	public function insertWith(Sql\Insert $insert) | int;
	public function update(
		array $set,
		Sql\Where|callable|string|array $where = null,
		array $joins = null
	) : int;
	public function updateWith(Sql\Update $update) : int;
	public function delete(Sql\Where|callable|string|array $where) : int;
	public function deleteWith(Sql\Delete $delete) : int;
	public function getLastInsertValue() : int;
}
