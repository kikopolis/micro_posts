<?php

declare(strict_types = 1);

namespace App\Event\Comment;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class RestoreEvent extends Event
{
	/**
	 * @var User
	 */
	private User $restorer;
	
	/**
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * CommentDeletedEvent constructor.
	 * @param  User     $restorer
	 * @param  Comment  $comment
	 */
	public function __construct(User $restorer, Comment $comment)
	{
		$this->restorer = $restorer;
		$this->comment  = $comment;
	}
	
	/**
	 * @return User
	 */
	public function getRestoredBy(): User
	{
		return $this->restorer;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}