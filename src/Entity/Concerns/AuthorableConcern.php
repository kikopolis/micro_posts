<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Entity\Contracts\AuthorableContract;
use App\Entity\User;

/**
 * Trait AuthorableConcern
 * @package App\Entity\Concerns
 */
trait AuthorableConcern
{
	/**
	 * @return null|User
	 */
	public function getAuthor(): ?User
	{
		return $this->author;
	}
	
	/**
	 * @param  User  $author
	 * @return $this|AuthorableContract
	 */
	public function setAuthor(User $author): self
	{
		$this->author = $author;
		
		return $this;
	}
}