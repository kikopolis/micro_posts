<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UnApproveEvent extends Event
{
	const NAME = 'post.un.approved';
	
	/**
	 * @var User
	 */
	private User $unApprovedBy;
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * ApproveEvent constructor.
	 * @param  User  $unApprovedBy
	 * @param  Post  $post
	 */
	public function __construct(User $unApprovedBy, Post $post)
	{
		$this->unApprovedBy = $unApprovedBy;
		$this->post         = $post;
	}
	
	/**
	 * @return User
	 */
	public function getUnApprovedBy(): User
	{
		return $this->unApprovedBy;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}