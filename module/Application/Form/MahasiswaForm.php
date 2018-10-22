<?php
namespace Application\Form;

use Zend\Form\Form;

class MahasiswaForm extends Form
{
	public function __construct($name = NULL)
	{
		parent::__construct('mahasiswa');
		
		$this->add([
			'name' => 'id',
			'type' => 'hidden',
		]);
		$this->add([
			'name' => 'nim',
			'type' => 'text',
			'options' => [
				'label' => 'Nim',
			],
		]);
		$this->add([
			'name' => 'nama',
			'type' => 'text',
			'options' => [
				'label' => 'Nama',
			],
		]);
		$this->add([
			'name' => 'submit',
			'type' => 'submit',
			'options' => [
				'value' => 'Go',
				'id' => 'submitbutton',
			],
		]);
	}
}
