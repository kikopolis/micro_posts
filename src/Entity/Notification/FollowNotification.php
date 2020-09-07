<?php

declare(strict_types = 1);

namespace App\Entity\Notification;

use App\Entity\Contracts\NotificationContract;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * Class FollowNotification
 * @package App\Entity\Notification
 */
class FollowNotification extends Notification implements NotificationContract
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 */
	protected User $follower;
	
	/**
	 * FollowNotification constructor.
	 * @param  User  $followed
	 * @param  User  $follower
	 */
	public function __construct(
		User $followed,
		User $follower
	)
	{
		$this->owner    = $followed;
		$this->follower = $follower;
	}
	
	/**
	 * @return User
	 */
	public function getFollower(): User
	{
		return $this->follower;
	}
	
	/**
	 * @return string
	 */
	public function getText(): string
	{
		return sprintf(
			'User "%s" has started following you.',
			$this->getFollower()->getUsername()
		);
	}
}
