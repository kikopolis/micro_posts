<?php

declare(strict_types = 1);

namespace App\Event\Comment;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UnReportEvent extends Event
{
	const NAME = 'mod.post.un.report';
	
	/**
	 * @var User
	 */
	private User $mod;
	
	/**
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * UnReportEvent constructor.
	 * @param  User     $mod
	 * @param  Comment  $comment
	 */
	public function __construct(User $mod, Comment $comment)
	{
		$this->mod     = $mod;
		$this->comment = $comment;
	}
	
	/**
	 * @return User
	 */
	public function getMod(): User
	{
		return $this->mod;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}