<?php

declare(strict_types = 1);

namespace App\Event\Comment;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UnApproveEvent extends Event
{
	const NAME = 'comment.un.approved';
	
	/**
	 * @var User
	 */
	private User $unApprovedBy;
	
	/**
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * ApproveEvent constructor.
	 * @param  User     $unApprovedBy
	 * @param  Comment  $comment
	 */
	public function __construct(User $unApprovedBy, Comment $comment)
	{
		$this->unApprovedBy = $unApprovedBy;
		$this->comment      = $comment;
	}
	
	/**
	 * @return User
	 */
	public function getUnApprovedBy(): User
	{
		return $this->unApprovedBy;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}