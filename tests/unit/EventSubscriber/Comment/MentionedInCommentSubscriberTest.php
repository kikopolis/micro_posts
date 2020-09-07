<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Comment;

use App\Entity\Comment;
use App\Entity\User;
use App\Event\Comment\MentionedInCommentEvent;
use App\Event\TimeStampableCreatedEvent;
use App\EventSubscriber\Comment\MentionedInCommentSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \App\EventSubscriber\Comment\MentionedInCommentSubscriber
 * Class MentionedInCommentSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Comment
 */
class MentionedInCommentSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = MentionedInCommentSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(MentionedInCommentEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testNotify()
	{
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'getAuthor' => Stub\Expected::once(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		/** @var MentionedInCommentEvent $event */
		$event = Stub::make(
			MentionedInCommentEvent::class,
			[
				'getComment'     => Stub\Expected::once($comment),
				'getTaggedUsers' => Stub\Expected::once(
					[
						Stub::make(User::class),
						Stub::make(User::class),
						Stub::make(User::class),
					]
				),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'persist' => Stub\Expected::exactly(3),
			],
			$this
		);
		
		/** @var TimeStampableCreatedEvent $secondEvent */
		$secondEvent = Stub::make(TimeStampableCreatedEvent::class);
		
		/** @var EventDispatcherInterface $ed */
		$ed = Stub::makeEmpty(
			EventDispatcherInterface::class,
			[
				'dispatch' => Stub\Expected::exactly(3, $secondEvent),
			],
			$this
		);
		
		$subscriber = new MentionedInCommentSubscriber($em, $ed);
		
		$subscriber->notify($event);
	}
}