<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Notification;

use App\Entity\Complaint;
use App\Entity\Notification\ComplaintCreatedNotification;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\Entity\Notification\ComplaintCreatedNotification
 * Class ComplaintCreatedNotificationTest
 * @package App\Tests\unit\Entity\Notification
 */
class ComplaintCreatedNotificationTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testConstructorParams()
	{
		/** @var Complaint $complaint */
		$complaint = Stub::make(Complaint::class);
		
		/** @var User $complainer */
		$complainer = Stub::make(User::class);
		
		/** @var User $owner */
		$owner = Stub::make(User::class);
		
		$note = new ComplaintCreatedNotification(
			$owner,
			$complaint,
			$complainer
		);
		
		$this->assertEquals(
			$owner,
			$note->getOwner()
		);
		$this->assertEquals(
			$complaint,
			$note->getComplaint()
		);
		$this->assertEquals(
			$complainer,
			$note->getComplainer()
		);
		$this->assertTrue($note->isModNote());
	}
}