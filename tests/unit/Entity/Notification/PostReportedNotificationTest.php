<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Notification;

use App\Entity\Notification\PostReportedNotification;
use App\Entity\Post;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\Entity\Notification\PostReportedNotification
 * Class PostReportedNotificationTest
 * @package App\Tests\unit\Entity\Notification
 */
class PostReportedNotificationTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testConstructorParams()
	{
		/** @var Post $post */
		$post = Stub::make(Post::class);
		
		/** @var User $reportedBy */
		$reportedBy = Stub::make(User::class);
		
		/** @var User $owner */
		$owner = Stub::make(User::class);
		
		$note = new PostReportedNotification(
			$owner,
			$post,
			$reportedBy
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
			$reportedBy,
			$note->getReportedBy()
		);
		$this->assertTrue($note->isModNote());
	}
}