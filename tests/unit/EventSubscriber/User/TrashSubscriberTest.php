<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Event\User\TrashEvent;
use App\EventSubscriber\User\TrashSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\User\TrashSubscriber
 * Class TrashSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class TrashSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = TrashSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(TrashEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testTrash()
	{
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'trash'        => Stub\Expected::once(
					Stub::make(User::class)
				),
				'setTrashedBy' => Stub\Expected::never(),
			],
			$this
		);
		
		/** @var TrashEvent $event */
		$event = Stub::make(
			TrashEvent::class,
			[
				'getUser'      => Stub\Expected::once($user),
				'getTrashedBy' => Stub\Expected::once(null),
			],
			$this
		);
		
		/** @var LoggerInterface $logger */
		$logger = Stub::makeEmpty(
			LoggerInterface::class,
			[
				'info' => Stub\Expected::never(),
			],
			$this
		);
		
		$subscriber = new TrashSubscriber($logger);
		
		$subscriber->trash($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testLog()
	{
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'getUsername' => Stub\Expected::once('username'),
				'getId'       => Stub\Expected::once(1),
			],
			$this
		);
		
		/** @var TrashEvent $event */
		$event = Stub::make(
			TrashEvent::class,
			[
				'getUser'      => Stub\Expected::once($user),
				'getTrashedBy' => Stub\Expected::once(null),
			],
			$this
		);
		
		/** @var LoggerInterface $logger */
		$logger = Stub::makeEmpty(
			LoggerInterface::class,
			[
				'info' => Stub\Expected::once(),
			],
			$this
		);
		
		$subscriber = new TrashSubscriber($logger);
		
		$subscriber->log($event);
	}
}