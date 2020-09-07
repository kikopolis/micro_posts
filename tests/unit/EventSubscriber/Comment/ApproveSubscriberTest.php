<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Comment;

use App\Entity\Comment;
use App\Entity\User;
use App\Event\Comment\ApproveEvent;
use App\EventSubscriber\Comment\ApproveSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\EventSubscriber\Comment\ApproveSubscriber
 * Class ApproveSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Comment
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
		$emptyComment = Stub::make(Comment::class);
		
		/** @var ApproveEvent $event */
		$event = Stub::make(
			ApproveEvent::class,
			[
				'getComment' => Stub\Expected::once(
					$emptyComment
				),
				
				'approve' => Stub\Expected::once(
					$emptyComment
				),
				
				'setApprovedBy' => Stub\Expected::once(
					$emptyComment
				),
				
				'getApprovedBy' => Stub\Expected::once(
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
		
		$subscriber = new ApproveSubscriber($logger);
		
		$subscriber->approve($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testLog()
	{
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
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
				'getComment'    => Stub\Expected::once($comment),
				'getApprovedBy' => Stub\Expected::once($user),
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
		
		$subscriber = new ApproveSubscriber($logger);
		
		$subscriber->log($event);
	}
}