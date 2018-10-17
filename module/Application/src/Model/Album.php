<?php
namespace Application\Model;

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