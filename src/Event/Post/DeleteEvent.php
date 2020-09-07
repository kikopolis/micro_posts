<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class DeleteEvent extends Event
{
	const NAME = 'post.deleted';
	
	/**
	 * @var User
	 */
	private User $deleter;
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * DeleteEvent constructor.
	 * @param  User  $deleter
	 * @param  Post  $post
	 */
	public function __construct(User $deleter, Post $post)
	{
		$this->deleter = $deleter;
		$this->post    = $post;
	}
	
	/**
	 * @return User
	 */
	public function getDeleter(): User
	{
		return $this->deleter;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}