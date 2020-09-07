<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Comment;

use App\Entity\Comment;
use App\Entity\User;
use App\Event\Comment\RestoreEvent;
use App\EventSubscriber\Comment\RestoreSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Comment\RestoreSubscriber
 * Class RestoreSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Comment
 */
class RestoreSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = RestoreSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(RestoreEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testRestore()
	{
		/** @var User $user */
		$user = Stub::make(User::class);
		
		/** @var Comment $emptyComment */
		$emptyComment = Stub::make(Comment::class);
		
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'restore'       => Stub\Expected::once($emptyComment),
				'setRestoredBy' => Stub\Expected::once($emptyComment),
			],
			$this
		);
		
		/** @var RestoreEvent $event */
		$event = Stub::make(
			RestoreEvent::class,
			[
				'getComment'    => Stub\Expected::once($comment),
				'getRestoredBy' => Stub\Expected::once($user),
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
		
		$subscriber = new RestoreSubscriber($logger);
		
		$subscriber->restore($event);
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
		
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'getId' => Stub\Expected::once(1),
			],
			$this
		);
		
		/** @var RestoreEvent $event */
		$event = Stub::make(
			RestoreEvent::class,
			[
				'getComment'    => Stub\Expected::once($comment),
				'getRestoredBy' => Stub\Expected::once($user),
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
		
		$subscriber = new RestoreSubscriber($logger);
		
		$subscriber->log($event);
	}
}