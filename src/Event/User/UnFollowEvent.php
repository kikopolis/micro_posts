<?php

declare(strict_types = 1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UnFollowEvent extends Event
{
	/**
	 * @var User
	 */
	private User $unFollower;
	
	/**
	 * @var User
	 */
	private User $unFollowed;
	
	/**
	 * UnFollowEvent constructor.
	 * @param  User  $unFollower
	 * @param  User  $unFollowed
	 */
	public function __construct(User $unFollower, User $unFollowed)
	{
		$this->unFollower = $unFollower;
		$this->unFollowed = $unFollowed;
	}
	
	/**
	 * @return User
	 */
	public function getUnFollower(): User
	{
		return $this->unFollower;
	}
	
	/**
	 * @return User
	 */
	public function getUnFollowed(): User
	{
		return $this->unFollowed;
	}
}