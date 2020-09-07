<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Event\User\UnFollowEvent;
use App\EventSubscriber\User\UnFollowSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @covers  \App\EventSubscriber\User\UnFollowSubscriber
 * Class UnFollowSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class UnFollowSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = UnFollowSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(
			UnFollowEvent::class,
			$result
		);
	}
	
	/**
	 * @throws Exception
	 */
	public function testUnFollow()
	{
		/** @var User $unFollowed */
		$unFollowed = Stub::make(
			User::class,
			[
				'getFollowers' => Stub\Expected::once(
					Stub::make(
						ArrayCollection::class,
						[
							'contains' => Stub\Expected::once(true),
						],
						$this
					)
				),
			],
			$this
		);
		
		/** @var User $unFollower */
		$unFollower = Stub::make(
			User::class,
			[
				'unFollow' => Stub\Expected::once(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		/** @var UnFollowEvent $event */
		$event = Stub::make(
			UnFollowEvent::class,
			[
				'getUnFollowed' => Stub\Expected::once($unFollowed),
				'getUnFollower' => Stub\Expected::once($unFollower),
			],
			$this
		);
		
		$subscriber = new UnFollowSubscriber();
		
		$subscriber->unFollow($event);
	}
}