<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Comment;

use App\Entity\Comment;
use App\Entity\User;
use App\Event\Comment\ReportEvent;
use App\Event\TimeStampableCreatedEvent;
use App\EventSubscriber\Comment\ReportSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \App\EventSubscriber\Comment\ReportSubscriber
 * Class ReportedSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Comment
 */
class ReportSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = ReportSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(ReportEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testReport()
	{
		/** @var User $user */
		$user = Stub::make(User::class);
		
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'report' => Stub\Expected::once(
					Stub::make(Comment::class)
				),
			],
			$this
		);
		
		/** @var ReportEvent $event */
		$event = Stub::make(
			ReportEvent::class,
			[
				'getComment'    => Stub\Expected::once($comment),
				'getReportedBy' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'persist' => Stub\Expected::never(),
			],
			$this
		);
		
		/** @var EventDispatcherInterface $ed */
		$ed = Stub::makeEmpty(EventDispatcherInterface::class);
		
		$subscriber = new ReportSubscriber($em, $ed);
		
		$subscriber->report($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testNotify()
	{
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'getAuthor' => Stub\Expected::exactly(
					3,
					Stub::make(User::class)
				),
			],
			$this
		);
		
		/** @var ReportEvent $event */
		$event = Stub::make(
			ReportEvent::class,
			[
				'getComment' => Stub\Expected::once($comment),
				'getMods'    => Stub\Expected::once(
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
		
		$subscriber = new ReportSubscriber($em, $ed);
		
		$subscriber->notify($event);
	}
}