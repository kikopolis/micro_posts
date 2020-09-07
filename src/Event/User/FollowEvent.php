<?php

declare(strict_types = 1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class FollowEvent extends Event
{
	const NAME = 'followed.user';
	
	/**
	 * @var User
	 */
	private User $follower;
	
	/**
	 * @var User
	 */
	private User $followed;
	
	/**
	 * FollowEvent constructor.
	 * @param  User  $follower
	 * @param  User  $followed
	 */
	public function __construct(User $follower, User $followed)
	{
		$this->follower = $follower;
		$this->followed = $followed;
	}
	
	/**
	 * @return User
	 */
	public function getFollower(): User
	{
		return $this->follower;
	}
	
	/**
	 * @return User
	 */
	public function getFollowed(): User
	{
		return $this->followed;
	}
}