<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Event\User\DeleteEvent;
use App\EventSubscriber\User\DeleteSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers  \App\EventSubscriber\User\DeleteSubscriber
 * Class DeleteSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
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
		/** @var User $user */
		$user = Stub::make(User::class);
		
		/** @var DeleteEvent $event */
		$event = Stub::make(
			DeleteEvent::class,
			[
				'getUser' => Stub\Expected::once($user),
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
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'remove' => Stub\Expected::once(),
			],
			$this
		);
		
		$subscriber = new DeleteSubscriber(
			$em,
			$logger
		);
		
		$subscriber->delete($event);
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
		
		/** @var User $admin */
		$admin = Stub::make(
			User::class,
			[
				'getUsername' => Stub\Expected::once('admin'),
				'getId'       => Stub\Expected::once(2),
			],
			$this
		);
		
		/** @var DeleteEvent $event */
		$event = Stub::make(
			DeleteEvent::class,
			[
				'getUser'  => Stub\Expected::once($user),
				'getDeletedBy' => Stub\Expected::once($admin),
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
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'remove' => Stub\Expected::never(),
			],
			$this
		);
		
		$subscriber = new DeleteSubscriber(
			$em,
			$logger
		);
		
		$subscriber->log($event);
	}
}