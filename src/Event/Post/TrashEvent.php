<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class TrashEvent extends Event
{
	const NAME = 'post.trashed';
	
	/**
	 * @var User
	 */
	private User $trasher;
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * DeleteEvent constructor.
	 * @param  User  $trasher
	 * @param  Post  $post
	 */
	public function __construct(User $trasher, Post $post)
	{
		$this->trasher = $trasher;
		$this->post    = $post;
	}
	
	/**
	 * @return User
	 */
	public function getTrashedBy(): User
	{
		return $this->trasher;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}