<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\UnApproveEvent;
use App\EventSubscriber\Post\UnApproveSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Post\UnApproveSubscriber
 * Class UnApproveSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
 */
class UnApproveSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = UnApproveSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(UnApproveEvent::class, $result);
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
				'setApprovedBy'   => Stub\Expected::once($emptyPost),
				'setUnApprovedBy' => Stub\Expected::once($emptyPost),
				'unApprove'       => Stub\Expected::once($emptyPost),
			],
			$this
		);
		
		/** @var UnApproveEvent $event */
		$event = Stub::make(
			UnApproveEvent::class,
			[
				'getPost'         => Stub\Expected::once($post),
				'getUnApprovedBy' => Stub\Expected::once(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		$subscriber = new UnApproveSubscriber($logger);
		
		$subscriber->unApprove($event);
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
		
		/** @var UnApproveEvent $event */
		$event = Stub::make(
			UnApproveEvent::class,
			[
				'getPost'         => Stub\Expected::once($post),
				'getUnApprovedBy' => Stub\Expected::once($user),
			],
			$this
		);
		
		$subscriber = new UnApproveSubscriber($logger);
		
		$subscriber->log($event);
	}
}