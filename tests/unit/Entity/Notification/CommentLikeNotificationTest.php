<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Notification;

use App\Entity\Comment;
use App\Entity\Notification\CommentLikeNotification;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\Entity\Notification\CommentLikeNotification
 * Class CommentLikeNotificationTest
 * @package App\Tests\unit\Entity\Notification
 */
class CommentLikeNotificationTest extends Unit
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
		
		/** @var Comment $comment */
		$comment = Stub::make(Comment::class);
		
		$note = new CommentLikeNotification(
			$owner,
			$comment,
			$likedBy
		);
		
		$this->assertEquals(
			$owner,
			$note->getOwner()
		);
		$this->assertEquals(
			$comment,
			$note->getComment()
		);
		$this->assertEquals(
			$likedBy,
			$note->getLikedBy()
		);
	}
}