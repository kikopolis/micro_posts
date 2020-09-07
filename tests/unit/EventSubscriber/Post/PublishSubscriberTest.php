<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\PublishEvent;
use App\EventSubscriber\Post\PublishSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Post\PublishSubscriber
 * Class PublishSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
 */
class PublishSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = PublishSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(PublishEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testPublish()
	{
		/** @var LoggerInterface $logger */
		$logger = Stub::makeEmpty(
			LoggerInterface::class,
			[
				'info' => Stub\Expected::never(),
			],
			$this
		);
		
		/** @var Post $emptyPost */
		$emptyPost = Stub::make(Post::class);
		
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'publish'        => Stub\Expected::once($emptyPost),
				'setPublishedBy' => Stub\Expected::once($emptyPost),
			],
			$this
		);
		
		/** @var PublishEvent $event */
		$event = Stub::make(
			PublishEvent::class,
			[
				'getPost'        => Stub\Expected::once($post),
				'getPublishedBy' => Stub\Expected::once(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		$subscriber = new PublishSubscriber($logger);
		
		$subscriber->publish($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testLog()
	{
		/** @var LoggerInterface $logger */
		$logger = Stub::makeEmpty(
			LoggerInterface::class,
			[
				'info' => Stub\Expected::once(),
			],
			$this
		);
		
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'getId' => Stub\Expected::once(1),
			],
			$this
		);
		
		/** @var PublishEvent $event */
		$event = Stub::make(
			PublishEvent::class,
			[
				'getPost'        => Stub\Expected::once($post),
				'getPublishedBy' => Stub\Expected::once(
					Stub::make(
						User::class,
						[
							'getUsername' => Stub\Expected::once('username'),
							'getId'       => Stub\Expected::once(1),
						],
						$this
					)
				),
			],
			$this
		);
		
		$subscriber = new PublishSubscriber($logger);
		
		$subscriber->log($event);
	}
}