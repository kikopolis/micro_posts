<?php

declare(strict_types = 1);

namespace App\Tests\integration\User;

use App\Entity\Notification\FollowNotification;
use App\Entity\User;
use App\Event\User\DeleteEvent;
use App\Event\User\FollowEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class DeleteFollowerUserStaysTest
 * @package App\Tests\integration\User
 */
class DeleteFollowerUserStaysTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testDeleteFollowerUserStays()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$followed = $em->find(User::class, 1);
		$follower = $em->find(User::class, 2);
		
		$ed->dispatch(
			new FollowEvent($follower, $followed)
		);
		
		// assertions
		static::assertTrue(
			$followed->getFollowers()->contains($follower)
		);
		
		static::assertTrue(
			$follower->getFollowing()->contains($followed)
		);
		
		// note delete the user
		$ed->dispatch(
			new DeleteEvent($follower, $follower)
		);
		
		$em->flush();
		
		// assertions
		$followed = $em->find(User::class, 1);
		$follower = $em->find(User::class, 2);
		
		static::assertNull($follower);
		static::assertFalse($follower instanceof User);
		static::assertTrue($followed instanceof User);
		static::assertTrue($followed->getId() === 1);
		
		$this->tester->cantSeeInRepository(
			FollowNotification::class,
			[
				'owner' => 1,
			]
		);
		
		static::assertFalse(
			$followed->getFollowers()->contains($follower)
		);
	}
}