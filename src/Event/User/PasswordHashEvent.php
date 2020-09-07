<?php

declare(strict_types = 1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class PasswordHashEvent extends Event
{
	public const NAME = 'password.needs.hashing';
	
	/**
	 * @var User
	 */
	private User $user;
	
	/**
	 * PasswordNeedsHashingEvent constructor.
	 * @param  User  $user
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
	}
	
	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}
}