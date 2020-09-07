<?php

declare(strict_types = 1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class DeleteEvent extends Event
{
	const NAME = 'user.deleted';
	
	/**
	 * @var User
	 */
	private User $user;
	
	/**
	 * @var User
	 */
	private User $deletedBy;
	
	/**
	 * DeleteEvent constructor.
	 * @param  User  $user
	 * @param  User  $deletedBy
	 */
	public function __construct(User $user, User $deletedBy)
	{
		$this->user      = $user;
		$this->deletedBy = $deletedBy;
	}
	
	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}
	
	/**
	 * @return User
	 */
	public function getDeletedBy(): User
	{
		return $this->deletedBy;
	}
}