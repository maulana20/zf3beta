<?php
namespace Application\Controller;

use Application\Controller\ParentController;
use Application\Model\Mahasiswa;
use Application\Form\MahasiswaForm;

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
	
	public function addAction()
	{
		$form = new MahasiswaForm();
		$form->get('submit')->setValue('Add');
		
		$request = $this->getRequest();
		if (! $request->isPost()) return ['form' => $form];
		//if (! $form->isValid()) return ['form' => $form];
		
		echo '<pre>'; var_dump($request); exit();
		$mahasiswa = new Mahasiswa();
		$data['nim'] = '';
		$data['nama'] = '';
		$mahasiswa->add($data);
	}
}
