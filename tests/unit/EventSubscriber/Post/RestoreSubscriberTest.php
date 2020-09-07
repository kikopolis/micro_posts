<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\RestoreEvent;
use App\EventSubscriber\Post\RestoreSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Post\RestoreSubscriber
 * Class RestoreSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
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
		/** @var Post $emptyPost */
		$emptyPost = Stub::make(Post::class);
		
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'restore'       => Stub\Expected::once($emptyPost),
				'setRestoredBy' => Stub\Expected::once($emptyPost),
			],
			$this
		);
		
		/** @var RestoreEvent $event */
		$event = Stub::make(
			RestoreEvent::class,
			[
				'getPost'       => Stub\Expected::once($post),
				'getRestoredBy' => Stub\Expected::once(
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
		
		$subscriber = new RestoreSubscriber($logger);
		
		$subscriber->restore($event);
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
		
		/** @var RestoreEvent $event */
		$event = Stub::make(
			RestoreEvent::class,
			[
				'getPost'       => Stub\Expected::once($post),
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