<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Comment;

use App\Entity\Comment;
use App\Entity\User;
use App\Event\Comment\TrashEvent;
use App\EventSubscriber\Comment\TrashSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Comment\TrashSubscriber
 * Class TrashSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Comment
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
		$emptyComment = Stub::make(Comment::class);
		
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'trash'        => Stub\Expected::once($emptyComment),
				'setTrashedBy' => Stub\Expected::once($emptyComment),
			],
			$this
		);
		
		/** @var TrashEvent $event */
		$event = Stub::make(
			TrashEvent::class,
			[
				'getComment'   => Stub\Expected::once($comment),
				'getTrashedBy' => Stub\Expected::once(
					Stub::make(User::class)
				),
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
		
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'getId' => Stub\Expected::once(1),
			],
			$this
		);
		
		/** @var TrashEvent $event */
		$event = Stub::make(
			TrashEvent::class,
			[
				'getComment'   => Stub\Expected::once($comment),
				'getTrashedBy' => Stub\Expected::once($user),
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