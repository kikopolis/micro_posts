<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ReportEvent extends Event
{
	const NAME = 'post.reported';
	
	/**
	 * @var array|User[]
	 */
	private array $mods;
	
	/**
	 * @var User
	 */
	private User $reporter;
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * ReportedEvent constructor.
	 * @param  array  $mods
	 * @param  User   $reporter
	 * @param  Post   $post
	 */
	public function __construct(array $mods, User $reporter, Post $post)
	{
		$this->reporter = $reporter;
		$this->post     = $post;
		$this->mods     = $mods;
	}
	
	/**
	 * @return User[]|array
	 */
	public function getMods()
	{
		return $this->mods;
	}
	
	/**
	 * @return User
	 */
	public function getReportedBy(): User
	{
		return $this->reporter;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}