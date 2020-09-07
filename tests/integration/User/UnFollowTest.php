<?php

declare(strict_types = 1);

namespace App\Tests\integration\User;

use App\Entity\Notification\FollowNotification;
use App\Entity\User;
use App\Event\User\FollowEvent;
use App\Event\User\UnFollowEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class UnFollowTest
 * @package App\Tests\integration\User
 */
class UnFollowTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	protected IntegrationTester $tester;
	
	public function testUnFollow()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		/** @var User $followed */
		$followed = $em->find(User::class, 1);
		
		/** @var User $follower */
		$follower = $em->find(User::class, 2);
		
		$ed->dispatch(
			new FollowEvent($follower, $followed)
		);
		
		$em->flush();
		
		// note get users again to refresh their collections
		/** @var User $followed */
		$followed = $em->find(User::class, 1);
		
		/** @var User $follower */
		$follower = $em->find(User::class, 2);
		
		// assertions
		$this->tester->canSeeInRepository(
			FollowNotification::class,
			[
				'owner' => $followed,
			]
		);
		
		static::assertTrue(
			$followed->getFollowers()->contains($follower)
		);
		
		static::assertTrue(
			$follower->getFollowing()->contains($followed)
		);
		
		$ed->dispatch(
			new UnFollowEvent($follower, $followed)
		);
		
		$em->flush();
		
		// note get users again to refresh their collections
		/** @var User $followed */
		$followed = $em->find(User::class, 1);
		
		/** @var User $follower */
		$follower = $em->find(User::class, 2);
		
		// assertions
		// todo figure out a way to remove notifications if they exist after unfollowing
		//		$this->tester->cantSeeInRepository(
		//			FollowNotification::class,
		//			[
		//				'owner' => $followed,
		//			]
		//		);
		
		static::assertFalse(
			$followed->getFollowers()->contains($follower)
		);
		
		static::assertFalse(
			$follower->getFollowing()->contains($followed)
		);
	}
}