<?php
namespace Album\Form;

use Zend\Form\Form;

class AlbumForm extends Form
{
	public function __construct($name = NULL)
	{
		parent::__construct('album');
		
		$this->add([
			'name' => 'id',
			'type' => 'hidden',
		]);
		$this->add([
			'name' => 'artist',
			'type' => 'text',
			'options' => [
				'label' => 'Artist',
			],
		]);
		$this->add([
			'name' => 'title',
			'type' => 'text',
			'options' => [
				'label' => 'Title',
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
