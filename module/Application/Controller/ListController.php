<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\PostRepository;

class ListController extends AbstractActionController
{
	private $postRepository;
	
	public function __construct(PostRepository $postRepository)
	{
		$this->postRepository = $postRepository;
	}
	
	public function indexAction()
	{
		return new ViewModel([
			'posts' => $this->postRepository->findAllPosts(),
		]);
	}
}
