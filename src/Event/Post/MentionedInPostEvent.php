<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class MentionedInPostEvent extends Event
{
	const NAME = 'mentioned.in.post';
	
	/**
	 * @var array|User[]
	 */
	private array $taggedUsers;
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * MentionedInPost constructor.
	 * @param  array|User[]  $taggedUsers
	 * @param  Post          $post
	 */
	public function __construct(array $taggedUsers, Post $post)
	{
		$this->taggedUsers = $taggedUsers;
		$this->post        = $post;
	}
	
	/**
	 * @return array|User[]
	 */
	public function getTaggedUsers(): array
	{
		return $this->taggedUsers;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}