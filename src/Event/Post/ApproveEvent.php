<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ApproveEvent extends Event
{
	const NAME = 'post.approved';
	
	/**
	 * @var User
	 */
	private User $approvedBy;
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * ApproveEvent constructor.
	 * @param  User  $approvedBy
	 * @param  Post  $post
	 */
	public function __construct(User $approvedBy, Post $post)
	{
		$this->approvedBy = $approvedBy;
		$this->post       = $post;
	}
	
	/**
	 * @return User
	 */
	public function getApprovedBy(): User
	{
		return $this->approvedBy;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}