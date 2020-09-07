<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Event\TimeStampableCreatedEvent;
use App\Event\User\FollowEvent;
use App\EventSubscriber\User\FollowSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @covers  \App\EventSubscriber\User\FollowSubscriber
 * Class FollowSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class FollowSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = FollowSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(FollowEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testFollow()
	{
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'persist' => Stub\Expected::once(),
			],
			$this
		);
		
		/** @var User $follower */
		$follower = Stub::make(
			User::class,
			[
				'follow' => Stub\Expected::once(
					Stub::make(
						User::class,
						[],
						$this
					)
				),
			],
			$this
		);
		
		/** @var User $followed */
		$followed = Stub::make(
			User::class,
			[
				'getFollowers' => Stub\Expected::once(
					Stub::makeEmpty(
						Collection::class,
						[
							'contains' => Stub\Expected::once(false),
						],
						$this
					)
				),
			],
			$this
		);
		
		/** @var FollowEvent $event */
		$event = Stub::make(
			FollowEvent::class,
			[
				'getFollowed' => $followed,
				'getFollower' => $follower,
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
		
		$subscriber = new FollowSubscriber($em, $ed);
		
		$subscriber->follow($event);
	}
}