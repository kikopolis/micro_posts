<?php

declare(strict_types = 1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class RestoreEvent
 * @package App\Event\User
 */
class RestoreEvent extends Event
{
	const NAME = 'user.restored';
	
	/**
	 * @var User
	 */
	private User $restored;
	
	/**
	 * @var User
	 */
	private User $restoredBy;
	
	/**
	 * RestoreEvent constructor.
	 * @param  User  $restored
	 * @param  User  $restoredBy
	 */
	public function __construct(User $restored, User $restoredBy)
	{
		$this->restored   = $restored;
		$this->restoredBy = $restoredBy;
	}
	
	/**
	 * @return User
	 */
	public function getRestored(): User
	{
		return $this->restored;
	}
	
	/**
	 * @return User
	 */
	public function getRestoredBy(): User
	{
		return $this->restoredBy;
	}
}