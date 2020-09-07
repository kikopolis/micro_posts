<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class PublishEvent extends Event
{
	const NAME = 'post.published';
	
	/**
	 * @var User
	 */
	private User $publishedBy;
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * PublishEvent constructor.
	 * @param  User  $publishedBy
	 * @param  Post  $post
	 */
	public function __construct(User $publishedBy, Post $post)
	{
		$this->publishedBy = $publishedBy;
		$this->post        = $post;
	}
	
	/**
	 * @return User
	 */
	public function getPublishedBy(): User
	{
		return $this->publishedBy;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}