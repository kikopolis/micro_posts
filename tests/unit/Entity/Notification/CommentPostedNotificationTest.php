<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Notification;

use App\Entity\Comment;
use App\Entity\Notification\CommentPostedNotification;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\Entity\Notification\CommentPostedNotification
 * Class CommentPostedNotificationTest
 * @package App\Tests\unit\Entity\Notification
 */
class CommentPostedNotificationTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testConstructorParams()
	{
		/** @var Comment $comment */
		$comment = Stub::make(Comment::class);
		
		/** @var User $postedBy */
		$postedBy = Stub::make(User::class);
		
		/** @var User $owner */
		$owner = Stub::make(User::class);
		
		$note = new CommentPostedNotification(
			$owner,
			$comment,
			$postedBy
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
			$postedBy,
			$note->getPostedBy()
		);
	}
}