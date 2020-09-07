<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Comment;

use App\Entity\Comment;
use App\Entity\User;
use App\Event\Comment\FollowedUserCommentsEvent;
use App\Event\TimeStampableCreatedEvent;
use App\EventSubscriber\Comment\FollowedCommentsSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \App\EventSubscriber\Comment\FollowedCommentsSubscriber
 * Class FollowedCreateSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Comment
 */
class FollowedCreateSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = FollowedCommentsSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(FollowedUserCommentsEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testNotify()
	{
		$comment = Stub::make(
			Comment::class,
			[
				'getAuthor' => Stub::make(User::class),
			],
			$this
		);
		
		/** @var FollowedUserCommentsEvent $event */
		$event = Stub::make(
			FollowedUserCommentsEvent::class,
			[
				'getComment'   => Stub\Expected::once($comment),
				'getFollowers' =>
					[
						Stub::make(User::class),
						Stub::make(User::class),
						Stub::make(User::class),
					],
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
				'dispatch'=>Stub\Expected::exactly(3, $secondEvent)
			],
			$this);
		
		$subscriber = new FollowedCommentsSubscriber($em, $ed);
		
		$subscriber->notify($event);
	}
}