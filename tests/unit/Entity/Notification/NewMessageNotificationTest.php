<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Notification;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Notification\NewMessageNotification;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\Entity\Notification\NewMessageNotification
 * Class NewMessageNotificationTest
 * @package App\Tests\unit\Entity\Notification
 */
class NewMessageNotificationTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testConstructorParams()
	{
		/** @var User $postedBy */
		$postedBy = Stub::make(User::class);
		
		/** @var User $owner */
		$owner = Stub::make(User::class);
		
		/** @var Message $message */
		$message = Stub::make(Message::class);
		
		/** @var Conversation $conversation */
		$conversation = Stub::make(Conversation::class);
		
		$note = new NewMessageNotification(
			$owner,
			$message,
			$conversation,
			$postedBy
		);
		
		$this->assertEquals(
			$owner,
			$note->getOwner()
		);
		$this->assertEquals(
			$message,
			$note->getMessage()
		);
		$this->assertEquals(
			$conversation,
			$note->getConversation()
		);
		$this->assertEquals(
			$postedBy,
			$note->getPostedBy()
		);
		
	}
}