<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\TrashEvent;
use App\EventSubscriber\Post\TrashSubscriber;
use Codeception\Test\Unit;
use Codeception\Stub;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Post\TrashSubscriber
 * Class TrashSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
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
		/** @var Post $emptyPost */
		$emptyPost = Stub::make(Post::class);
		
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'trash'        => Stub\Expected::once($emptyPost),
				'setTrashedBy' => Stub\Expected::once($emptyPost),
			],
			$this
		);
		
		/** @var TrashEvent $event */
		$event = Stub::make(
			TrashEvent::class,
			[
				'getPost'      => Stub\Expected::once($post),
				'getTrashedBy' => Stub\Expected::once(
					Stub::make(
						User::class,
						[],
						$this
					)
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
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'getId' => Stub\Expected::once(1),
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
		
		/** @var TrashEvent $event */
		$event = Stub::make(
			TrashEvent::class,
			[
				'getPost'      => Stub\Expected::once($post),
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