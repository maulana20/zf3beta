<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Model\MahasiswaModel;

class MahasiswaController extends AbstractActionController
{
	public function indexAction()
	{
		$mahasiswa = new MahasiswaModel();
		echo $mahasiswa->getData();
	}
}
