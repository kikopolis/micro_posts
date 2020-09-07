<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity;

use App\Entity\Notification;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\Entity\Notification
 * Class NotificationTest
 * @package App\Tests\unit\Entity
 */
class NotificationTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testDefaultProps()
	{
		$notification = $this->getMockForAbstractClass(Notification::class);
		
		$this->assertNull($notification->getId());
		$this->assertNull($notification->getOwner());
		$this->assertFalse($notification->isSeen());
		$this->assertFalse($notification->isModNote());
		$this->assertNull($notification->getCreatedAt());
		$this->assertNull($notification->getUpdatedAt());
	}
}