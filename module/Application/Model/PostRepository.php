<?php
namespace Application\Model;

use DomainException;

class PostRepository implements PostRepositoryInterface
{
	public $id;
	public $artist;
	public $text;
	
	public function exchangeArray(array $data)
	{
		$this->id    = !empty($data['id']) ? $data['id'] : null;
		$this->title = !empty($data['title']) ? $data['title'] : null;
		$this->text  = !empty($data['text']) ? $data['text'] : null;
	}
	
	public function getArrayCopy()
	{
		return [
			'id' => $this->id,
			'artist' => $this->artist,
			'text' => $this->title,
		];
	}
	
	public function findAllPosts()
	{
		return array_map(function ($post) {
			return new Post(
				$post['title'],
				$post['text'],
				$post['id']
			);
		}, $this->data);
	}
	
	public function findPost($id)
	{
		if (! isset($this->data[$id])) {
			throw new DomainException(sprintf('Post by id "%s" not found', $id));
		}
		
		return new Post(
			$this->data[$id]['title'],
			$this->data[$id]['text'],
			$this->data[$id]['id']
		);
	}
}
