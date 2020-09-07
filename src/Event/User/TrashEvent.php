<?php

declare(strict_types = 1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class TrashEvent extends Event
{
	const NAME = 'user.trashed';
	
	/**
	 * @var User
	 */
	private User $user;
	
	/**
	 * @var null|User
	 */
	private ?User $trashedBy;
	
	/**
	 * TrashEvent constructor.
	 * @param  User       $user
	 * @param  null|User  $trashedBy
	 */
	public function __construct(User $user, ?User $trashedBy = null)
	{
		$this->user      = $user;
		$this->trashedBy = $trashedBy;
	}
	
	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}
	
	/**
	 * @return null|User
	 */
	public function getTrashedBy(): ?User
	{
		return $this->trashedBy;
	}
}