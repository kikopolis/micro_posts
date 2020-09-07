<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UnPublishEvent extends Event
{
	const NAME = 'post.published';
	
	/**
	 * @var User
	 */
	private User $unPublishedBy;
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * PublishEvent constructor.
	 * @param  User  $unPublishedBy
	 * @param  Post  $post
	 */
	public function __construct(User $unPublishedBy, Post $post)
	{
		$this->unPublishedBy = $unPublishedBy;
		$this->post          = $post;
	}
	
	/**
	 * @return User
	 */
	public function getUnPublishedBy(): User
	{
		return $this->unPublishedBy;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}