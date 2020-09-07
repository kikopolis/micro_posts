<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Notification;

use App\Entity\Notification;
use App\Event\Notification\ReadEvent;
use App\EventSubscriber\Notification\ReadSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\EventSubscriber\Notification\ReadSubscriber
 * Class ReadSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Notification
 */
class ReadSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = ReadSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(ReadEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testRead()
	{
		/** @var Notification $notification */
		$notification = Stub::makeEmpty(
			Notification::class,
			[
				'setSeen' => Stub\Expected::once(
					Stub::makeEmpty(Notification::class)
				),
			],
			$this
		);
		
		/** @var ReadEvent $event */
		$event = Stub::make(
			ReadEvent::class,
			[
				'getNotification' => Stub\Expected::once($notification),
			],
			$this
		);
		
		$subscriber = new ReadSubscriber();
		
		$subscriber->read($event);
	}
}