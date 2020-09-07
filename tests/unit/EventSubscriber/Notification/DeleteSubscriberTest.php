<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Notification;

use App\Entity\Notification;
use App\Event\Notification\DeleteEvent;
use App\EventSubscriber\Notification\DeleteSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @covers \App\EventSubscriber\Notification\DeleteSubscriber
 * Class DeleteSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Notification
 */
class DeleteSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = DeleteSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(DeleteEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testDelete()
	{
		/** @var Notification $notification */
		$notification = Stub::makeEmpty(Notification::class);
		
		/** @var DeleteEvent $event */
		$event = Stub::make(
			DeleteEvent::class,
			[
				'getNotification' => Stub\Expected::once($notification),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'remove' => Stub\Expected::once(),
			],
			$this
		);
		
		$subscriber = new DeleteSubscriber($em);
		
		$subscriber->delete($event);
	}
}