<?php

declare(strict_types = 1);

namespace App\Event\Comment;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ApproveEvent extends Event
{
	const NAME = 'comment.approved';
	
	/**
	 * @var User
	 */
	private User $approvedBy;
	
	/**
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * ApproveEvent constructor.
	 * @param  User     $approvedBy
	 * @param  Comment  $comment
	 */
	public function __construct(User $approvedBy, Comment $comment)
	{
		$this->approvedBy = $approvedBy;
		$this->comment    = $comment;
	}
	
	/**
	 * @return User
	 */
	public function getApprovedBy(): User
	{
		return $this->approvedBy;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}