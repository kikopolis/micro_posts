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
 * Class UserMentionedInPostNotification
 * @package App\Entity\Notification
 */
class UserMentionedInPostNotification extends Notification implements NotificationContract
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Post")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 * @var Post
	 */
	protected Post $post;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 * @var User
	 */
	private User $postedBy;
	
	/**
	 * UserMentionedInPostNotification constructor.
	 * @param  User  $owner
	 * @param  Post  $post
	 * @param  User  $postedBy
	 */
	public function __construct(
		User $owner,
		Post $post,
		User $postedBy
	)
	{
		$this->owner    = $owner;
		$this->post     = $post;
		$this->postedBy = $postedBy;
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
			'User "%s" has mentioned you in a post.',
			$this->getPostedBy()->getUsername()
		);
	}
}