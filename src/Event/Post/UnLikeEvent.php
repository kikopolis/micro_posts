<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UnLikeEvent extends Event
{
	const NAME = 'post.un.liked';
	
	/**
	 * @var User
	 */
	private User $unLiker;
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * PostLikedEvent constructor.
	 * @param  User  $unLiker
	 * @param  Post  $post
	 */
	public function __construct(User $unLiker, Post $post)
	{
		$this->unLiker = $unLiker;
		$this->post    = $post;
	}
	
	/**
	 * @return User
	 */
	public function getUnlikedBy(): User
	{
		return $this->unLiker;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}