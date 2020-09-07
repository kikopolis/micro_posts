<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\CreateEvent;
use App\EventSubscriber\Post\CreateSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Post\CreateSubscriber
 * Class CreateSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
 */
class CreateSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = CreateSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(CreateEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testPublish()
	{
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'publish' => Stub\Expected::once(
					Stub::make(
						Post::class,
						[],
						$this
					)
				),
			],
			$this
		);
		
		/** @var CreateEvent $event */
		$event = Stub::make(
			CreateEvent::class,
			[
				'getPost' => Stub\Expected::once($post),
			],
			$this
		);
		
		/** @var LoggerInterface $logger */
		$logger = Stub::makeEmpty(
			LoggerInterface::class,
			[],
			$this
		);
		
		$subscriber = new CreateSubscriber($logger);
		
		$subscriber->publish($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testApprove()
	{
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'approve'       => Stub\Expected::never(
					Stub::make(Post::class)
				),
				'unApprove'     => Stub\Expected::once(
					Stub::make(Post::class)
				),
				'setApprovedBy' => Stub\Expected::never(
					Stub::make(Post::class)
				),
				'getAuthor'     => Stub\Expected::never(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		/** @var CreateEvent $event */
		$event = Stub::make(
			CreateEvent::class,
			[
				'getPost' => Stub\Expected::once($post),
			],
			$this
		);
		
		/** @var LoggerInterface $logger */
		$logger = Stub::makeEmpty(LoggerInterface::class);
		
		$subscriber = new CreateSubscriber($logger);
		
		$subscriber->approve($event);
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
				'getId'       => Stub\Expected::once(1),
				'getUsername' => Stub\Expected::once('username'),
			],
			$this
		);
		
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'getId'     => Stub\Expected::once(1),
				'getAuthor' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var CreateEvent $event */
		$event = Stub::make(
			CreateEvent::class,
			[
				'getPost' => Stub\Expected::once($post),
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
		
		$subscriber = new CreateSubscriber($logger);
		
		$subscriber->log($event);
	}
}