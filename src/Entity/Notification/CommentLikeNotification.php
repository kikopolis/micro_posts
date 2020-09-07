<?php

namespace App\Entity\Notification;

use App\Entity\Contracts\NotificationContract;
use App\Entity\Notification;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * Class CommentLikeNotification
 * @package App\Entity\Notification
 */
class CommentLikeNotification extends Notification implements NotificationContract
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Comment")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 * @var Comment
	 */
	protected Comment $comment;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 * @var User
	 */
	protected User $likedBy;
	
	/**
	 * CommentLikeNotification constructor.
	 * @param  User     $owner
	 * @param  User     $likedBy
	 * @param  Comment  $comment
	 */
	public function __construct(
		User $owner,
		Comment $comment,
		User $likedBy
	)
	{
		$this->owner   = $owner;
		$this->comment = $comment;
		$this->likedBy = $likedBy;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): ?Comment
	{
		return $this->comment;
	}
	
	/**
	 * @return null|User
	 */
	public function getLikedBy(): ?User
	{
		return $this->likedBy;
	}
	
	/**
	 * @return string
	 */
	public function getText(): string
	{
		return sprintf(
			'User "%s" has liked your comment.',
			$this->getLikedBy()->getUsername()
		);
	}
}
