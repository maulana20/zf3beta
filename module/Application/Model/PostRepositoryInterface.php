<?php
namespace Application\Model;

interface PostRepositoryInterface
{
	public function findAllPosts();
	public function findPost($id);
}
