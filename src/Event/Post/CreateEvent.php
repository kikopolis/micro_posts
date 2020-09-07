<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class CreateEvent
 * @package App\Event\Post
 */
class CreateEvent extends Event
{
	const NAME = 'post.created';
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * PostCreatedEvent constructor.
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