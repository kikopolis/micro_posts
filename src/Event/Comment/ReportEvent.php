<?php

declare(strict_types = 1);

namespace App\Event\Comment;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ReportEvent extends Event
{
	const NAME = 'comment.reported';
	
	/**
	 * @var array|User[]
	 */
	private array $mods;
	
	/**
	 * @var User
	 */
	private User $reporter;
	
	/**
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * CommentReportedEvent constructor.
	 * @param  array    $mods
	 * @param  User     $reporter
	 * @param  Comment  $comment
	 */
	public function __construct(array $mods, User $reporter, Comment $comment)
	{
		$this->mods     = $mods;
		$this->reporter = $reporter;
		$this->comment  = $comment;
	}
	
	/**
	 * @return User[]|array
	 */
	public function getMods()
	{
		return $this->mods;
	}
	
	/**
	 * @return User
	 */
	public function getReportedBy(): User
	{
		return $this->reporter;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}