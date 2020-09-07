<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Notification;

use App\Entity\Comment;
use App\Entity\Notification\CommentReportedNotification;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\Entity\Notification\CommentReportedNotification
 * Class CommentReportedNotificationTest
 * @package App\Tests\unit\Entity\Notification
 */
class CommentReportedNotificationTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testConstructorParams()
	{
		
		/** @var Comment $comment */
		$comment = Stub::make(Comment::class);
		
		/** @var User $reportedBy */
		$reportedBy = Stub::make(User::class);
		
		/** @var User $owner */
		$owner = Stub::make(User::class);
		
		$note = new CommentReportedNotification(
			$owner,
			$comment,
			$reportedBy
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
			$reportedBy,
			$note->getReportedBy()
		);
		$this->assertTrue($note->isModNote());
	}
}