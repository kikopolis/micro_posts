<?php

declare(strict_types = 1);

namespace App\Event\Comment;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class DeleteEvent extends Event
{
	const NAME = 'comment.deleted';
	
	/**
	 * @var User
	 */
	private User $deleter;
	
	/**
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * CommentDeletedEvent constructor.
	 * @param  User     $deleter
	 * @param  Comment  $comment
	 */
	public function __construct(User $deleter, Comment $comment)
	{
		$this->deleter = $deleter;
		$this->comment = $comment;
	}
	
	/**
	 * @return User
	 */
	public function getDeleter(): User
	{
		return $this->deleter;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}