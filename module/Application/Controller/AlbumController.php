<?php
namespace Application\Controller;

use Application\Model\AlbumTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Application\Form\AlbumForm;
use Application\Model\Album;

class AlbumController extends ParentController
{
	private $table;
	
	public function __construct(AlbumTable $table)
	{
		$this->table = $table;
	}
	
	public function indexAction()
	{
		$paginator = $this->table->fetchAll(true);
		
		$page = (int) $this->params()->fromQuery('page', 1);
		$page = ($page < 1) ? 1 : $page;
		$paginator->setCurrentPageNumber($page);
		
		$paginator->setItemCountPerPage(10);
		
		return new ViewModel(['paginator' => $paginator]);
		//return new ViewModel([
		//	'albums' => $this->table->fetchAll(),
		//]);
	}

	public function addAction()
	{
		$form = new AlbumForm();
		$form->get('submit')->setValue('Add');
		
        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $album = new Album();
        $form->setInputFilter($album->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
			//echo json_encode($form->getMessages());
            return ['form' => $form];
        }
        
		$album->exchangeArray($form->getData());
		$this->table->saveAlbum($album);
        return $this->redirect()->toRoute('album', ['action' => 'index']);
	}

	public function editAction()
	{
		$id = (int) $this->params()->fromRoute('id', 0);
		
		if (0 === $id) {
			return $this->redirect()->toRoute('album', ['action' => 'add']);
		}
		
		try {
			$album = $this->table->getAlbum($id);
		} catch (\Exception $e) {
			return $this->redirect()->toRoute('album', ['action' => 'index']);
		}
		
		$form = new AlbumForm();
		$form->bind($album);
		$form->get('submit')->setAttribute('value', 'Edit');
		
		$request = $this->getRequest();
		$viewData = ['id' => $id, 'form' => $form];
		
		if (! $request->isPost()) {
			return $viewData;
			//return $this->redirect()->toRoute('album', ['action' => 'add']);
		}
		
		$form->setInputFilter($album->getInputFilter());
		$form->setData($request->getPost());
		
		if (! $form->isValid()) {
			return $viewData;
		}
		
		$this->table->saveAlbum($album);
		
		return $this->redirect()->toROute('album', ['action' => 'index']);
	}

	public function deleteAction()
	{
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
			return $this->redirect()->toRoute('album');
		}
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$del = $request->getPost('del', 'No');
			
			if ($del == 'Yes') {
				$id = $request->getPost('id');
				$this->table->deleteAlbum($id);
			}
			
			return $this->redirect()->toRoute('album');
		}
		
		return [
			'id' => $id,
			'album' => $this->table->getAlbum($id),
		];
	}
}
