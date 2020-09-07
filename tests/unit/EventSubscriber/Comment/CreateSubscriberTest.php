<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Comment;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Event\Comment\CreateEvent;
use App\Event\TimeStampableCreatedEvent;
use App\EventSubscriber\Comment\CreateSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @covers  \App\EventSubscriber\Comment\CreateSubscriber
 * Class CreateSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Comment
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
	public function testApprove()
	{
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'approve'       => Stub\Expected::never(
					Stub::make(Comment::class)
				),
				'unApprove'     => Stub\Expected::once(
					Stub::make(Comment::class)
				),
				'setApprovedBy' => Stub\Expected::never(
					Stub::make(Comment::class)
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
				'getComment' => Stub\Expected::once($comment),
			],
			$this
		);
		
		/** @var LoggerInterface $logger */
		$logger = Stub::makeEmpty(LoggerInterface::class);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(EntityManagerInterface::class);
		
		/** @var EventDispatcherInterface $ed */
		$ed = Stub::makeEmpty(EventDispatcherInterface::class);
		
		$subscriber = new CreateSubscriber($em, $logger, $ed);
		
		$subscriber->approve($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testNotifyPostAuthor()
	{
		/** @var User $postAuthor */
		$postAuthor = Stub::make(User::class);
		
		/** @var User $commentAuthor */
		$commentAuthor = Stub::make(User::class);
		
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'getAuthor' => Stub\Expected::once($postAuthor),
			],
			$this
		);
		
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'getPost' => Stub\Expected::once($post),
				
				'getAuthor' => Stub\Expected::once($commentAuthor),
			],
			$this
		);
		
		/** @var CreateEvent $event */
		$event = Stub::make(
			CreateEvent::class,
			[
				'getComment' => Stub\Expected::once($comment),
			],
			$this
		);
		
		/** @var LoggerInterface $logger */
		$logger = Stub::makeEmpty(LoggerInterface::class);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'persist' => Stub\Expected::once(),
			],
			$this
		);
		
		/** @var EventDispatcherInterface $ed */
		$ed = Stub::makeEmpty(
			EventDispatcherInterface::class,
			[
				'dispatch' => Stub\Expected::once(
					Stub::make(TimeStampableCreatedEvent::class)
				),
			],
			$this
		);
		
		$subscriber = new CreateSubscriber($em, $logger, $ed);
		
		$subscriber->notifyPostAuthor($event);
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
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(EntityManagerInterface::class);
		
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
				'getPost'   => Stub\Expected::once(
					Stub::make(
						Post::class,
						[
							'getId' => Stub\Expected::once(1),
						],
						$this
					)
				),
				'getAuthor' => Stub\Expected::once($user),
				'getId'     => Stub\Expected::once(1),
			],
			$this
		);
		
		/** @var CreateEvent $event */
		$event = Stub::make(
			CreateEvent::class,
			[
				'getComment' => Stub\Expected::once($comment),
			],
			$this
		);
		
		/** @var EventDispatcherInterface $ed */
		$ed = Stub::makeEmpty(EventDispatcherInterface::class);
		
		$subscriber = new CreateSubscriber($em, $logger, $ed);
		
		$subscriber->log($event);
	}
}