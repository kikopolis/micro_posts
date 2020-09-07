<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Notification;

use App\Entity\Notification;
use App\Event\Notification\MassReadEvent;
use App\EventSubscriber\Notification\MassReadSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\EventSubscriber\Notification\MassReadSubscriber
 * Class MassReadSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Notification
 */
class MassReadSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = MassReadSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(MassReadEvent::class, $result);
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
				'setSeen' => Stub\Expected::exactly(
					3,
					Stub::makeEmpty(Notification::class)
				),
			],
			$this
		);
		
		/** @var MassReadEvent $event */
		$event = Stub::make(
			MassReadEvent::class,
			[
				'getNotifications' => Stub\Expected::once(
					[
						$notification,
						$notification,
						$notification,
					]
				),
			],
			$this
		);
		
		$subscriber = new MassReadSubscriber();
		
		$subscriber->read($event);
	}
}