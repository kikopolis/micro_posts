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
 * Class FollowedUserPostsNotification
 * @package App\Entity\Notification
 */
class FollowedUserPostsNotification extends Notification implements NotificationContract
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
	private User $postedBy;
	
	/**
	 * FollowedUserPosts constructor.
	 * @param  User  $follower
	 * @param  Post  $post
	 * @param  User  $postedBy
	 */
	public function __construct(
		User $follower,
		Post $post,
		User $postedBy
	)
	{
		$this->owner    = $follower;
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
			'User "%s" who you follow, has created a new MicroPost.',
			$this->getPostedBy()->getId()
		);
	}
}