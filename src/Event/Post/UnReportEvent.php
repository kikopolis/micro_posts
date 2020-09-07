<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UnReportEvent extends Event
{
	const NAME = 'mod.post.un.report';
	
	/**
	 * @var User
	 */
	private User $mod;
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * UnReportEvent constructor.
	 * @param  User  $mod
	 * @param  Post  $post
	 */
	public function __construct(User $mod, Post $post)
	{
		$this->mod  = $mod;
		$this->post = $post;
	}
	
	/**
	 * @return User
	 */
	public function getMod(): User
	{
		return $this->mod;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}