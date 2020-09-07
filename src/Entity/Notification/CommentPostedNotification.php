<?php

namespace App\Entity\Notification;

use App\Entity\Comment;
use App\Entity\Contracts\NotificationContract;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * Class CommentPostedNotification
 * @package App\Entity\Notification
 */
class CommentPostedNotification extends Notification implements NotificationContract
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
	protected User $postedBy;
	
	/**
	 * CommentPostedNotification constructor.
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
	 * @return User
	 */
	public function getPostedBy(): User
	{
		return $this->postedBy;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): ?Comment
	{
		return $this->comment;
	}
	
	/**
	 * @return string
	 */
	public function getText(): string
	{
		return sprintf(
			'User "%s" has posted a new comment on your post.',
			$this->getPostedBy()->getUsername()
		);
	}
}
