<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\PostsTable;
use Application\Model\postRepository;

class ListController extends AbstractActionController
{
	private $table;
	
	public function __construct(PostsTable $table)
	{
		$this->table = $table;
	}
	
	public function indexAction()
	{
		$paginator = $this->table->fetchAll(true);
		
		$page = (int) $this->params()->fromQuery('page', 1);
		$page = ($page < 1) ? 1 : $page;
		$paginator->setCurrentPageNumber($page);
		
		$paginator->setItemCountPerPage(MAX_PAGE);
		
		return new ViewModel(['paginator' => $paginator]);
	}
	
	public function addAction()
	{
		$request = $this->getRequest();
		if (! $request->isPost()) {
			return new ViewModel();
		}
		$post_data = $request->getPost();
		$postRepository = new postRepository();
		$postRepository->exchangeArray(array('title' => $post_data['title'], 'text' => $post_data['text']));
		var_dump($postRepository); exit();
		$this->table->savePost($postRepository);
        return $this->redirect()->toRoute('blog', ['action' => 'index']);
	}
	
	public function editAction()
	{}
	
	public function deleteAction()
	{}
}
