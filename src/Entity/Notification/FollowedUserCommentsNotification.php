<?php

declare(strict_types = 1);

namespace App\Entity\Notification;

use App\Entity\Comment;
use App\Entity\Contracts\NotificationContract;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * Class FollowedUserCommentsNotification
 * @package App\Entity\Notification
 */
class FollowedUserCommentsNotification extends Notification implements NotificationContract
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Comment")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 * @var Comment
	 */
	private Comment $comment;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 * @var User
	 */
	private User $postedBy;
	
	/**
	 * FollowedUserCreatesACommentNotification constructor.
	 * @param  User     $owner
	 * @param  Comment  $comment
	 * @param  User     $postedBy
	 */
	public function __construct(
		User $owner,
		Comment $comment,
		User $postedBy
	)
	{
		$this->owner    = $owner;
		$this->comment  = $comment;
		$this->postedBy = $postedBy;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
	
	/**
	 * @return User
	 */
	public function getPostedBy(): User
	{
		return $this->postedBy;
	}
	
	/**
	 * @return string
	 */
	public function getText(): string
	{
		return sprintf(
			'User "%s" who you follow, has commented on a post.',
			$this->getPostedBy()->getUsername()
		);
	}
}