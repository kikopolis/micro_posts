<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\ApproveEvent;
use App\EventSubscriber\Post\ApproveSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Post\ApproveSubscriber
 * Class ApproveSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
 */
class ApproveSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = ApproveSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(ApproveEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testApprove()
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
				'approve'       => Stub\Expected::once($emptyPost),
				'setApprovedBy' => Stub\Expected::once($emptyPost),
			],
			$this
		);
		
		/** @var ApproveEvent $event */
		$event = Stub::make(
			ApproveEvent::class,
			[
				'getPost'       => Stub\Expected::once($post),
				'getApprovedBy' => Stub\Expected::once(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		$subscriber = new ApproveSubscriber($logger);
		
		$subscriber->approve($event);
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
		
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'getUsername' => Stub\Expected::once('username'),
				'getId'       => Stub\Expected::once(1),
			],
			$this
		);
		
		/** @var ApproveEvent $event */
		$event = Stub::make(
			ApproveEvent::class,
			[
				'getPost'       => Stub\Expected::once($post),
				'getApprovedBy' => Stub\Expected::once($user),
			],
			$this
		);
		
		$subscriber = new ApproveSubscriber($logger);
		
		$subscriber->log($event);
	}
}