<?php

declare(strict_types = 1);

namespace App\Tests\integration\User;

use App\Entity\Notification\FollowNotification;
use App\Entity\User;
use App\Event\User\FollowEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class FollowTest
 * @package App\Tests\integration\User
 */
class FollowTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	protected IntegrationTester $tester;
	
	public function testFollow()
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
	}
}