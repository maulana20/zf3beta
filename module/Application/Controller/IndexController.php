<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;

class IndexController extends ParentController
{
    public function indexAction()
    {
		$viewModel = new ViewModel();
		$viewModel->setTemplate('application/index/index'); 
        return $viewModel;
    }
}
