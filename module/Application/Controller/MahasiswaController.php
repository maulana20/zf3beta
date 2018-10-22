<?php
namespace Application\Controller;

use Application\Controller\ParentController;
use Application\Model\Mahasiswa;

class MahasiswaController extends ParentController
{
	public function indexAction()
	{
		$mahasiswa = new Mahasiswa();
		$page = (int) $this->params()->fromQuery('page', 1);
		$page = ($page < 1) ? 1 : $page;
		$mahasiswa_list = $mahasiswa->getList($page, MAX_PAGE);
		
		return new $this->view(['list' => $mahasiswa_list]);
	}
}
