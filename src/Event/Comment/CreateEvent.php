<?php

declare(strict_types = 1);

namespace App\Event\Comment;

use App\Entity\Comment;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class CreateEvent
 * @package App\Event\Comment
 */
class CreateEvent extends Event
{
	const NAME = 'comment.created';
	
	/**
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * CommentCreatedEvent constructor.
	 * @param  Comment  $comment
	 */
	public function __construct(Comment $comment)
	{
		$this->comment = $comment;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}