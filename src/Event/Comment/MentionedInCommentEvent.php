<?php

declare(strict_types = 1);

namespace App\Event\Comment;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class MentionedInCommentEvent extends Event
{
	const NAME = 'mentioned.in.comment';
	
	/**
	 * @var array|User[]
	 */
	private array $taggedUsers;
	
	/**
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * MentionedInCommentEvent constructor.
	 * @param  array    $taggedUsers
	 * @param  Comment  $comment
	 */
	public function __construct(array $taggedUsers, Comment $comment)
	{
		$this->taggedUsers = $taggedUsers;
		$this->comment     = $comment;
	}
	
	/**
	 * @return array|User[]
	 */
	public function getTaggedUsers(): array
	{
		return $this->taggedUsers;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}