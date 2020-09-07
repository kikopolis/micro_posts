<?php

declare(strict_types = 1);

namespace App\Entity\Notification;

use App\Entity\Contracts\NotificationContract;
use App\Entity\Post;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * Class PostLikeNotification
 * @package App\Entity\Notification
 */
class PostLikeNotification extends Notification implements NotificationContract
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
	protected User $likedBy;
	
	/**
	 * PostLikeNotification constructor.
	 * @param  User  $owner
	 * @param  Post  $post
	 * @param  User  $likedBy
	 */
	public function __construct(
		User $owner,
		Post $post,
		User $likedBy
	)
	{
		$this->owner   = $owner;
		$this->post    = $post;
		$this->likedBy = $likedBy;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): ?Post
	{
		return $this->post;
	}
	
	/**
	 * @return User
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
			'User "%s" has liked your post.',
			$this->getLikedBy()->getUsername()
		);
	}
}
