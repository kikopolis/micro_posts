<?php

declare(strict_types = 1);

namespace App\Event\Comment;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class TrashEvent extends Event
{
	const NAME = 'comment.trashed';
	
	/**
	 * @var User
	 */
	private User $trasher;
	
	/**
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * CommentTrashedEvent constructor.
	 * @param  User     $trasher
	 * @param  Comment  $comment
	 */
	public function __construct(User $trasher, Comment $comment)
	{
		$this->trasher = $trasher;
		$this->comment = $comment;
	}
	
	/**
	 * @return User
	 */
	public function getTrashedBy(): User
	{
		return $this->trasher;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}