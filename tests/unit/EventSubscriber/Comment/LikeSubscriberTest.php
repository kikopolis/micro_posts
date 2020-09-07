<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Comment;

use App\Entity\Comment;
use App\Entity\User;
use App\Event\Comment\LikeEvent;
use App\Event\TimeStampableCreatedEvent;
use App\EventSubscriber\Comment\LikeSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @covers  \App\EventSubscriber\Comment\LikeSubscriber
 * Class LikeSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Comment
 */
class LikeSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = LikeSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(LikeEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testLike()
	{
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'like'       => Stub\Expected::once(
					Stub::make(Comment::class)
				),
				'getAuthor'  => Stub\Expected::once(
					Stub::make(
						User::class,
						[
							'getId' => Stub\Expected::once(1),
						],
						$this
					)
				),
				'getLikedBy' => Stub\Expected::once(
					Stub::make(
						ArrayCollection::class,
						[
							'contains' => Stub\Expected::once(false),
						],
						$this
					)
				),
			],
			$this
		);
		
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'getId' => Stub\Expected::once(2),
			],
			$this
		);
		
		/** @var LikeEvent $event */
		$event = Stub::make(
			LikeEvent::class,
			[
				'getComment' => Stub\Expected::once($comment),
				'getLiker'   => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(EntityManagerInterface::class);
		
		/** @var EventDispatcherInterface $ed */
		$ed = Stub::makeEmpty(EventDispatcherInterface::class);
		
		$subscriber = new LikeSubscriber($em, $ed);
		
		$subscriber->like($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function notify()
	{
		$user = Stub::make(User::class);
		
		/** @var LikeEvent $event */
		$event = Stub::make(
			LikeEvent::class,
			[
				'getComment' => Stub\Expected::once(
					Stub::make(Comment::class)
				),
				'getLiker'   => Stub\Expected::once($user),
				'getAuthor'  => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'persist' => Stub\Expected::once(),
			],
			$this
		);
		
		/** @var TimeStampableCreatedEvent $secondEvent */
		$secondEvent = Stub::make(TimeStampableCreatedEvent::class);
		
		/** @var EventDispatcherInterface $ed */
		$ed = Stub::makeEmpty(
			EventDispatcherInterface::class,
			[
				'dispatch' => Stub\Expected::once($secondEvent),
			],
			$this
		);
		
		$subscriber = new LikeSubscriber($em, $ed);
		
		$subscriber->notify($event);
	}
}