<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Notification;

use App\Entity\Notification\PostLikeNotification;
use App\Entity\Post;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\Entity\Notification\PostLikeNotification
 * Class PostLikeNotificationTest
 * @package App\Tests\unit\Entity\Notification
 */
class PostLikeNotificationTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testConstructorParams()
	{
		/** @var User $likedBy */
		$likedBy = Stub::make(User::class);
		
		/** @var User $owner */
		$owner = Stub::make(User::class);
		
		/** @var Post $post */
		$post = Stub::make(Post::class);
		
		$note = new PostLikeNotification(
			$owner,
			$post,
			$likedBy
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
			$likedBy,
			$note->getLikedBy()
		);
	}
}