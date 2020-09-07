<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\UnReportEvent;
use App\EventSubscriber\Post\UnReportSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Post\UnReportSubscriber
 * Class UnReportSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
 */
class UnReportSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = UnReportSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(UnReportEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testUnReport()
	{
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'unReport' => Stub\Expected::once(
					Stub::make(Post::class)
				),
			],
			$this
		);
		
		/** @var UnReportEvent $event */
		$event = Stub::make(
			UnReportEvent::class,
			[
				'getPost' => Stub\Expected::once($post),
				'getMod'  => Stub\Expected::never(),
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
		
		$subscriber = new UnReportSubscriber($logger);
		
		$subscriber->unReport($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testLog()
	{
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'unReport' => Stub\Expected::never(),
				'getId'    => Stub\Expected::once(1),
			],
			$this
		);
		
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'getUsername' => Stub\Expected::once('username'),
				'getId'       => Stub\Expected::once(1),
			],
			$this
		);
		
		/** @var UnReportEvent $event */
		$event = Stub::make(
			UnReportEvent::class,
			[
				'getPost' => Stub\Expected::once($post),
				'getMod'  => Stub\Expected::once($user),
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
		
		$subscriber = new UnReportSubscriber($logger);
		
		$subscriber->log($event);
	}
}