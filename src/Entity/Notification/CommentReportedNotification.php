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
 * Class CommentReportedNotification
 * @package App\Entity\Notification
 */
class CommentReportedNotification extends Notification implements NotificationContract
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
	private User $reportedBy;
	
	/**
	 * CommentReportedNotification constructor.
	 * @param  User     $owner
	 * @param  Comment  $comment
	 * @param  User     $reportedBy
	 */
	public function __construct(
		User $owner,
		Comment $comment,
		User $reportedBy
	)
	{
		$this->owner      = $owner;
		$this->comment    = $comment;
		$this->reportedBy = $reportedBy;
		
		$this->setIsModNote(true);
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
	public function getReportedBy(): User
	{
		return $this->reportedBy;
	}
	
	/**
	 * @return string
	 */
	public function getText(): string
	{
		return sprintf(
			'User "%s" has reported a post.',
			$this->getReportedBy()->getUsername()
		);
	}
}