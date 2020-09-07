<?php

declare(strict_types = 1);

namespace App\Event\Comment;

use App\Entity\Comment;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class UpdateEvent
 * @package App\Event\Comment
 */
class UpdateEvent extends Event
{
	const NAME = 'comment.updated';
	
	/**
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * UpdateEvent constructor.
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