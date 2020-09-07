<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\LikeEvent;
use App\Event\TimeStampableCreatedEvent;
use App\EventSubscriber\Post\LikeSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \App\EventSubscriber\Post\LikeSubscriber
 * Class LikeSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
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
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'like' => Stub\Expected::once(
					Stub::make(Post::class)
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
				'getPost'  => Stub\Expected::once($post),
				'getLiker' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'persist' => Stub\Expected::never(),
			],
			$this
		);
		
		/** @var EventDispatcherInterface $ed */
		$ed = Stub::makeEmpty(
			EventDispatcherInterface::class,
			[
				'dispatch' => Stub\Expected::never(),
			],
			$this
		);
		
		$subscriber = new LikeSubscriber($em, $ed);
		
		$subscriber->like($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testNotify()
	{
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'getAuthor' => Stub\Expected::once(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		/** @var LikeEvent $event */
		$event = Stub::make(
			LikeEvent::class,
			[
				'getPost'  => Stub\Expected::once($post),
				'getLiker' => Stub\Expected::once(
					Stub::make(User::class)
				),
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