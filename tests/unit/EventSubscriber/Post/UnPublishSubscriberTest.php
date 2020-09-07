<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\UnPublishEvent;
use App\EventSubscriber\Post\UnPublishSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Post\UnPublishSubscriber
 * Class UnPublishSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
 */
class UnPublishSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = UnPublishSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(UnPublishEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testUnPublish()
	{
		/** @var Post $emptyPost */
		$emptyPost = Stub::make(Post::class);
		
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'unPublish'        => Stub\Expected::once($emptyPost),
				'setUnPublishedBy' => Stub\Expected::once($emptyPost),
			],
			$this
		);
		
		/** @var UnPublishEvent $event */
		$event = Stub::make(
			UnPublishEvent::class,
			[
				'getPost'          => Stub\Expected::once($post),
				'getUnPublishedBy' => Stub\Expected::once(
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
		
		$subscriber = new UnPublishSubscriber($logger);
		
		$subscriber->unPublish($event);
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
		
		/** @var UnPublishEvent $event */
		$event = Stub::make(
			UnPublishEvent::class,
			[
				'getPost'          => Stub\Expected::once($post),
				'getUnPublishedBy' => Stub\Expected::once($user),
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
		
		$subscriber = new UnPublishSubscriber($logger);
		
		$subscriber->log($event);
	}
}