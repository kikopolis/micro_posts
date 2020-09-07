<?php

declare(strict_types = 1);

namespace App\Entity\Notification;

use App\Entity\Contracts\NotificationContract;
use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * Class PostReportedNotification
 * @package App\Entity\Notification
 */
class PostReportedNotification extends Notification implements NotificationContract
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Post")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 * @var User
	 */
	private User $reportedBy;
	
	/**
	 * PostReportedNotification constructor.
	 * @param  User  $owner
	 * @param  Post  $post
	 * @param  User  $reportedBy
	 */
	public function __construct(
		User $owner,
		Post $post,
		User $reportedBy
	)
	{
		$this->owner      = $owner;
		$this->post       = $post;
		$this->reportedBy = $reportedBy;
		
		$this->setIsModNote(true);
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
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