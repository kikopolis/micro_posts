<?php

declare(strict_types = 1);

namespace App\Event\Comment;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class FollowedUserCommentsEvent extends Event
{
	const NAME = 'followed.user.comments';
	
	/**
	 * @var array|User[]
	 */
	private array $followers;
	
	/**
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * FollowedUserComments constructor.
	 * @param  array    $followers
	 * @param  Comment  $comment
	 */
	public function __construct(array $followers, Comment $comment)
	{
		$this->followers = $followers;
		$this->comment   = $comment;
	}
	
	/**
	 * @return array|User[]
	 */
	public function getFollowers(): array
	{
		return $this->followers;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}