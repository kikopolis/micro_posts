<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Comment;

use App\Entity\Comment;
use App\Entity\User;
use App\Event\Comment\DeleteEvent;
use App\EventSubscriber\Comment\DeleteSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Comment\DeleteSubscriber
 * Class DeleteSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Comment
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
		/** @var DeleteEvent $event */
		$event = Stub::make(
			DeleteEvent::class,
			[
				'getComment' => Stub\Expected::once(
					Stub::make(Comment::class)
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
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'remove' => Stub\Expected::once(),
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
		
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'getAuthor' => Stub\Expected::once($user),
				'getId'     => Stub\Expected::once(1),
			],
			$this
		);
		
		/** @var DeleteEvent $event */
		$event = Stub::make(
			DeleteEvent::class,
			[
				'getComment' => Stub\Expected::once($comment),
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
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'remove' => Stub\Expected::never(),
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