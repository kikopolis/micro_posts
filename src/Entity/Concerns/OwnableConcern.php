<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Entity\Contracts\OwnableContract;
use App\Entity\User;

/**
 * Class OwnableConcern
 * @package App\Entity\Concerns
 */
trait OwnableConcern
{
	/**
	 * @return null|User
	 */
	public function getOwner(): ?User
	{
		return $this->owner;
	}
	
	/**
	 * @param  User  $owner
	 * @return $this|OwnableContract
	 */
	public function setOwner(User $owner): self
	{
		$this->owner = $owner;
		
		return $this;
	}
}