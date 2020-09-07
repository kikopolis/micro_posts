<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use Symfony\Contracts\EventDispatcher\Event;

class ViewEvent extends Event
{
	const NAME = 'post.viewed';
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * PostViewedEvent constructor.
	 * @param  Post  $post
	 */
	public function __construct(Post $post)
	{
		$this->post = $post;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}