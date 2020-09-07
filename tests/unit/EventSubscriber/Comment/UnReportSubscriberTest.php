<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Comment;

use App\Entity\Comment;
use App\Entity\User;
use App\Event\Comment\UnReportEvent;
use App\EventSubscriber\Comment\UnReportSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Comment\UnReportSubscriber
 * Class UnReportSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Comment
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
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'unReport' => Stub\Expected::once(
					Stub::make(Comment::class)
				),
			],
			$this
		);
		
		/** @var UnReportEvent $event */
		$event = Stub::make(
			UnReportEvent::class,
			[
				'getComment' => Stub\Expected::once($comment),
				'getMod'     => Stub\Expected::never(),
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
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
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
				'getComment' => Stub\Expected::once($comment),
				'getMod'     => Stub\Expected::once($user),
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