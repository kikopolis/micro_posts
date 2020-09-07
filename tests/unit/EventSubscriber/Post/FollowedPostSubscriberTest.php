<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\FollowedUserPostsEvent;
use App\Event\TimeStampableCreatedEvent;
use App\EventSubscriber\Post\FollowedPostSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \App\EventSubscriber\Post\FollowedPostSubscriber
 * Class FollowedPostSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
 */
class FollowedPostSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = FollowedPostSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(FollowedUserPostsEvent::class, $result);
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
				'getAuthor' => Stub::make(User::class),
			],
			$this
		);
		
		/** @var User $user */
		$user = Stub::make(User::class);
		
		/** @var FollowedUserPostsEvent $event */
		$event = Stub::make(
			FollowedUserPostsEvent::class,
			[
				'getPost'      => Stub\Expected::once($post),
				'getFollowers' => Stub\Expected::once(
					[
						$user,
						$user,
						$user,
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
		
		$subscriber = new FollowedPostSubscriber($em, $ed);
		
		$subscriber->notify($event);
	}
}