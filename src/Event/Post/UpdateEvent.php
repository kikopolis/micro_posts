<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class UpdateEvent
 * @package App\Event\Post
 */
class UpdateEvent extends Event
{
	const NAME = 'post.updated';
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * UpdateEvent constructor.
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