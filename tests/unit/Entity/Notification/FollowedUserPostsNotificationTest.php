<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Notification;

use App\Entity\Notification\FollowedUserPostsNotification;
use App\Entity\Post;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\Entity\Notification\FollowedUserPostsNotification
 * Class FollowNotificationTest
 * @package App\Tests\unit\Entity\Notification
 */
class FollowedUserPostsNotificationTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testConstructorParams()
	{
		/** @var Post $post */
		$post = Stub::make(Post::class);
		
		/** @var User $postedBy */
		$postedBy = Stub::make(User::class);
		
		/** @var User $owner */
		$owner = Stub::make(User::class);
		
		$note = new FollowedUserPostsNotification(
			$owner,
			$post,
			$postedBy
		);
		
		$this->assertEquals(
			$owner,
			$note->getOwner()
		);
		$this->assertEquals(
			$post,
			$note->getPost()
		);
		$this->assertEquals(
			$postedBy,
			$note->getPostedBy()
		);
	}
}