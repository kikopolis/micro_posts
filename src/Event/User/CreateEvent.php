<?php

declare(strict_types = 1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class CreateEvent
 * @package App\Event\User
 */
class CreateEvent extends Event
{
	const NAME = 'user.created';
	
	/**
	 * @var User
	 */
	private User $user;
	
	/**
	 * UserCreatedEvent constructor.
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