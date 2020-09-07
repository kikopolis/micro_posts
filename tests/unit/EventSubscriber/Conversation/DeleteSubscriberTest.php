<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Conversation;

use App\Entity\Conversation;
use App\Event\Conversation\DeleteEvent;
use App\EventSubscriber\Conversation\DeleteSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @covers  \App\EventSubscriber\Complaint\DeleteSubscriber
 * Class DeleteSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Conversation
 */
class DeleteSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = DeleteSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(
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
				'getConversation' => Stub\Expected::once(
					Stub::make(Conversation::class)
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