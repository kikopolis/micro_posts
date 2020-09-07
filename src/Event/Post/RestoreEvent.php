<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class RestoreEvent extends Event
{
	const NAME = 'post.restored';
	
	/**
	 * @var User
	 */
	private User $restorer;
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * RestoreEvent constructor.
	 * @param  User  $restorer
	 * @param  Post  $post
	 */
	public function __construct(User $restorer, Post $post)
	{
		$this->restorer = $restorer;
		$this->post     = $post;
	}
	
	/**
	 * @return User
	 */
	public function getRestoredBy(): User
	{
		return $this->restorer;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}