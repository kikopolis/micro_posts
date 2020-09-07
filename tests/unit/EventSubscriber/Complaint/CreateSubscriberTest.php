<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Complaint;

use App\Entity\Complaint;
use App\Entity\User;
use App\Event\Complaint\CreateEvent;
use App\Event\TimeStampableCreatedEvent;
use App\EventSubscriber\Complaint\CreateSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \App\EventSubscriber\Complaint\CreateSubscriber
 * Class CreateSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Complaint
 */
class CreateSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = CreateSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(CreateEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testNotify()
	{
		/** @var Complaint $complaint */
		$complaint = Stub::make(
			Complaint::class,
			[
				'getAuthor' => Stub\Expected::once(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		/** @var CreateEvent $event */
		$event = Stub::make(
			CreateEvent::class,
			[
				'getComplaint' => Stub\Expected::once($complaint),
				'getAdmins'    => Stub\Expected::once(
					[
						Stub::make(User::class),
						Stub::make(User::class),
						Stub::make(User::class),
					]
				),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'persist' => Stub\Expected::exactly(3),
			],
			$this
		);
		
		/** @var TimeStampableCreatedEvent $secondEvent */
		$secondEvent = Stub::make(TimeStampableCreatedEvent::class);
		
		/** @var EventDispatcherInterface $ed */
		$ed = Stub::makeEmpty(
			EventDispatcherInterface::class,
			[
				'dispatch' => Stub\Expected::exactly(3, $secondEvent),
			],
			$this
		);
		
		$subscriber = new CreateSubscriber($em, $ed);
		
		$subscriber->notify($event);
	}
}