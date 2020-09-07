<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\DeleteEvent;
use App\EventSubscriber\Post\DeleteSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Post\DeleteSubscriber
 * Class DeleteSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
 */
class DeleteSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = DeleteSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(DeleteEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testDelete()
	{
		/** @var Post $post */
		$post = Stub::make(Post::class);
		
		/** @var DeleteEvent $event */
		$event = Stub::make(
			DeleteEvent::class,
			[
				'getPost' => Stub\Expected::once($post),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'remove' => Stub\Expected::once(),
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
		
		$subscriber = new DeleteSubscriber(
			$em,
			$logger
		);
		
		$subscriber->delete($event);
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
		
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'getId' => Stub\Expected::once(1),
			],
			$this
		);
		
		/** @var DeleteEvent $event */
		$event = Stub::make(
			DeleteEvent::class,
			[
				'getPost'    => Stub\Expected::once($post),
				'getDeleter' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'remove' => Stub\Expected::never(),
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
		
		$subscriber = new DeleteSubscriber(
			$em,
			$logger
		);
		
		$subscriber->log($event);
	}
}