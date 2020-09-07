<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Message;

use App\Entity\Message;
use App\Event\Message\DeleteEvent;
use App\EventSubscriber\Message\DeleteSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @covers  \App\EventSubscriber\Message\DeleteSubscriber
 * Class DeleteSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Message
 */
class DeleteSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = DeleteSubscriber::getSubscribedEvents();
		
		static::assertArrayHasKey(
			DeleteEvent::class,
			$result
		);
	}
	
	/**
	 * @throws Exception
	 */
	public function testDelete()
	{
		/** @var DeleteEvent $event */
		$event = Stub::make(
			DeleteEvent::class,
			[
				'getMessage' => Stub\Expected::once(
					Stub::make(Message::class)
				),
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