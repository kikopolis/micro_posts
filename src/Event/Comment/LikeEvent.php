<?php

declare(strict_types = 1);

namespace App\Event\Comment;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class LikeEvent extends Event
{
	const NAME = 'comment.liked';
	
	/**
	 * @var User
	 */
	private User $liker;
	
	/**
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * LikeEvent constructor.
	 * @param  User     $liker
	 * @param  Comment  $comment
	 */
	public function __construct(User $liker, Comment $comment)
	{
		$this->liker   = $liker;
		$this->comment = $comment;
	}
	
	/**
	 * @return User
	 */
	public function getLiker(): User
	{
		return $this->liker;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}