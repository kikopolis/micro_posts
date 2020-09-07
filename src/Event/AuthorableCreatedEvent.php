<?php

declare(strict_types = 1);

namespace App\Event;

use App\Entity\Contracts\AuthorableContract;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class AuthorableCreatedEvent extends Event
{
	public const NAME = 'authorable.created';
	
	/**
	 * @var AuthorableContract
	 */
	private AuthorableContract $authorable;
	
	/**
	 * @var User
	 */
	private User $author;
	
	/**
	 * AuthorableCreatedEvent constructor.
	 * @param  AuthorableContract  $authorable
	 * @param  User                $author
	 */
	public function __construct(
		AuthorableContract $authorable,
		User $author
	)
	{
		$this->authorable = $authorable;
		$this->author     = $author;
	}
	
	/**
	 * @return AuthorableContract
	 */
	public function getAuthorable(): AuthorableContract
	{
		return $this->authorable;
	}
	
	/**
	 * @return User
	 */
	public function getAuthor(): User
	{
		return $this->author;
	}
}