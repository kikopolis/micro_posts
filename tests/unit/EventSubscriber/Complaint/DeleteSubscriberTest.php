<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Complaint;

use App\Entity\Complaint;
use App\Entity\User;
use App\Event\Complaint\DeleteEvent;
use App\EventSubscriber\Complaint\DeleteSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Complaint\DeleteSubscriber
 * Class DeleteSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Complaint
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
		/** @var Complaint $complaint */
		$complaint = Stub::make(Complaint::class);
		
		/** @var DeleteEvent $event */
		$event = Stub::make(
			DeleteEvent::class,
			[
				'getComplaint' => Stub\Expected::once($complaint),
				'getDeletedBy' => Stub\Expected::never(),
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
		/** @var User $deletedBy */
		$deletedBy = Stub::make(
			User::class,
			[
				'getUsername' => Stub\Expected::once('username'),
				'getId'       => Stub\Expected::once(1),
			],
			$this
		);
		
		/** @var Complaint $complaint */
		$complaint = Stub::make(
			Complaint::class,
			[
				'getId' => Stub\Expected::once(2),
			],
			$this
		);
		
		/** @var DeleteEvent $event */
		$event = Stub::make(
			DeleteEvent::class,
			[
				'getComplaint' => Stub\Expected::once($complaint),
				'getDeletedBy' => Stub\Expected::once($deletedBy),
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