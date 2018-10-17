<?php
namespace Application\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class AlbumTable
{
	private $tableGateway;

	public function __construct(TableGatewayInterface $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll($paginated = false)
	{
		if ($paginated) {
			return $this->fetchPaginatedResults();
		}
		return $this->tableGateway->select();
	}
	
	private function fetchPaginatedResults()
	{
		$select = new Select($this->tableGateway->getTable());
		
		$resultSetPrototype = new ResultSet();
		$resultSetPrototype->setArrayObjectPrototype(new Album());
		
		$paginatorAdapter = new DbSelect(
			$select,
			$this->tableGateway->getAdapter(),
			$resultSetPrototype
		);
		
		$paginator = new Paginator($paginatorAdapter);
		return $paginator;
	}

	public function getAlbum($id)
	{
		$id = (int) $id;
		$rowset = $this->tableGateway->select(['id' => $id]);
		$row = $rowset->current();
		if (! $row) {
			throw new RuntimeException(sprintf(
				'Could not find row with identifier %d',
				$id
			));
		}

		return $row;
	}

	public function saveAlbum(Album $album)
	{
		$data = [
			'artist' => $album->artist,
			'title'  => $album->title,
		];

		$id = (int) $album->id;

		if ($id === 0) {
			$this->tableGateway->insert($data);
			return;
		}

		if (! $this->getAlbum($id)) {
			throw new RuntimeException(sprintf(
				'Cannot update album with identifier %d; does not exist',
				$id
			));
		}

		$this->tableGateway->update($data, ['id' => $id]);
	}

	public function deleteAlbum($id)
	{
		$this->tableGateway->delete(['id' => (int) $id]);
	}
}

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

class Album implements InputFilterAwareInterface
{
	public $id;
	public $artist;
	public $title;
	
	private $inputFilter;

	public function exchangeArray(array $data)
	{
		$this->id     = !empty($data['id']) ? $data['id'] : null;
		$this->artist = !empty($data['artist']) ? $data['artist'] : null;
		$this->title  = !empty($data['title']) ? $data['title'] : null;
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new DomainException(sprintf(
			'%s does not allow injection of an alternate input filter',
			__CLASS__
		));
	}
	
	public function getInputFilter()
	{
		if ($this->inputFilter) {
			return $this->inputFilter;
		}
		
		$inputFilter = new inputFilter();
		
		// karena primary key maka tidak perlu di tambahkan
		/*$inputFilter->add([
			'name' => 'id',
			'required' => true,
			'filter' => [
				['name' => ToInt::class],
			],
		]);*/
		
		$inputFilter->add([
			'name' => 'artist',
			'required' => true,
			'filter' => [
				['name' => StripTags::class],
				['name' => StringTrim::class],
			],
			'validator' => [
				[
					'name' => StringLength::class,
					'option' => [
						'encoding' => 'UTF-8',
						'min' => 1,
						'max' => 100,
					],
				],
			],
		]);
		
		$inputFilter->add([
			'name' => 'title',
			'required' => true,
			'filter' => [
				['name' => StripTags::class],
				['name' => StringTrim::class],
			],
			'validator' => [
				[
					'name' => StringLength::class,
					'option' => [
						'encoding' => 'UTF-8',
						'min' => 1,
						'max' => 100,
					],
				],
			],
		]);
		
		$this->inputFilter = $inputFilter;
		return $this->inputFilter;
	}
	
	public function getArrayCopy()
	{
		return [
			'id' => $this->id,
			'artist' => $this->artist,
			'title' => $this->title,
		];
	}
}
