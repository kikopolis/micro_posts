<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

use App\Entity\User;

/**
 * Interface OwnableContract
 * @package App\Entity\Contracts
 */
interface OwnableContract
{
	/**
	 * @return null|User
	 */
	public function getOwner(): ?User;
	
	/**
	 * @param  User  $owner
	 * @return $this|OwnableContract
	 */
	public function setOwner(User $owner): OwnableContract;
}