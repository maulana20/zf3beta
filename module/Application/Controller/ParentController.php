<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ParentController extends AbstractActionController
{
	public $view = NULL;
	function init()
	{
		$this->view = new ViewModel();
	}
}
