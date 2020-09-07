<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\ReportEvent;
use App\Event\TimeStampableCreatedEvent;
use App\EventSubscriber\Post\ReportSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \App\EventSubscriber\Post\ReportSubscriber
 * Class ReportSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
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
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'report' => Stub\Expected::once(
					Stub::make(Post::class)
				),
			],
			$this
		);
		
		/** @var ReportEvent $event */
		$event = Stub::make(
			ReportEvent::class,
			[
				'getPost' => Stub\Expected::once($post),
				
				'getReportedBy' => Stub\Expected::once(
					Stub::make(User::class)
				),
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
		$ed = Stub::makeEmpty(
			EventDispatcherInterface::class,
			[
				'dispatch' => Stub\Expected::never(),
			],
			$this
		);
		
		$subscriber = new ReportSubscriber($em, $ed);
		
		$subscriber->report($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testNotify()
	{
		/** @var User $user */
		$user = Stub::make(User::class);
		
		/** @var ReportEvent $event */
		$event = Stub::make(
			ReportEvent::class,
			[
				'getReportedBy' => Stub\Expected::once(
					Stub::make(User::class)
				),
				'getPost'       => Stub\Expected::once(
					Stub::make(Post::class)
				),
				'getMods'       => Stub\Expected::once(
					[
						$user,
						$user,
						$user,
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
		
		$secondEvent = Stub::make(
			TimeStampableCreatedEvent::class
		);
		
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