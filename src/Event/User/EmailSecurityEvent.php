<?php

declare(strict_types = 1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class EmailSecurityEvent extends Event
{
	const NAME = 'email.security.notice';
	
	/**
	 * @var User
	 */
	private User $user;
	
	/**
	 * PasswordSecurityEvent constructor.
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