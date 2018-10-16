<?php

namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ListController extends AbstractActionController
{
	
	public function indexAction()
	{
		 return new ViewModel([
            'posts' => 'Hello, nyet',
        ]);
	}
}
