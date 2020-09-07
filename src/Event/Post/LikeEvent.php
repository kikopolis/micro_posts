<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class LikeEvent extends Event
{
	const NAME = 'post.liked';
	
	/**
	 * @var User
	 */
	private User $liker;
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * PostLikedEvent constructor.
	 * @param  User  $liker
	 * @param  Post  $post
	 */
	public function __construct(User $liker, Post $post)
	{
		$this->liker = $liker;
		$this->post  = $post;
	}
	
	/**
	 * @return User
	 */
	public function getLiker(): User
	{
		return $this->liker;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}