<?php
namespace Application\Controller;

use Application\Controller\ParentController;
use Application\Model\Mahasiswa;

class MahasiswaController extends ParentController
{
	public function indexAction()
	{
		$mahasiswa = new Mahasiswa();
		return new $this->view(['list' => $mahasiswa->getList()]);
	}
}
