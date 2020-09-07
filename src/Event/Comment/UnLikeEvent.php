<?php

declare(strict_types = 1);

namespace App\Event\Comment;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class UnLikeEvent
 * @package App\Event\Comment
 */
class UnLikeEvent extends Event
{
	const NAME = 'comment.un.liked';
	
	/**
	 * @var User
	 */
	private User $unLikedBy;
	
	/**
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * UnLikeEvent constructor.
	 * @param  User     $unLikedBy
	 * @param  Comment  $comment
	 */
	public function __construct(User $unLikedBy, Comment $comment)
	{
		$this->unLikedBy = $unLikedBy;
		$this->comment   = $comment;
	}
	
	/**
	 * @return User
	 */
	public function getUnLikedBy(): User
	{
		return $this->unLikedBy;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}