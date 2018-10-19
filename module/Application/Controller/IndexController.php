<?php
namespace Application\Controller;

class IndexController extends ParentController
{
    public function indexAction()
    {
        return $this->view;
    }
}
