<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

use App\Entity\User;

/**
 * Interface AuthorableContract
 * @package App\Entity\Contracts
 */
interface AuthorableContract
{
	/**
	 * @return null|User
	 */
	public function getAuthor(): ?User;
	
	/**
	 * @param  User  $author
	 * @return $this|AuthorableContract
	 */
	public function setAuthor(User $author): AuthorableContract;
}