<?php
namespace Application\Controller;

use Application\Controller\ParentController;
use Application\Model\MahasiswaModel;

class MahasiswaController extends ParentController
{
	public function indexAction()
	{
		$mahasiswa = new MahasiswaModel();
		echo $mahasiswa->getData();
	}
}