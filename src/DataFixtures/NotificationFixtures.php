<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Entity\Notification\FollowNotification;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

/**
 * @codeCoverageIgnore
 * Class NotificationFixtures
 * @package App\DataFixtures
 */
class NotificationFixtures extends BaseFixture implements DependentFixtureInterface
{
	/**
	 * @param  ObjectManager  $manager
	 * @throws Exception
	 */
	protected function loadData(ObjectManager $manager): void
	{
		for ($i = 0; $i < 500; $i++) {
			
			/** @var User $followed */
			$followed = $this->getRandomReference(User::class);
			
			/** @var User $follower */
			$follower = $this->getRandomReference(User::class);
			
			$follower->follow($followed);
			
			$note = new FollowNotification($followed, $follower);
			
			$note->setOwner($followed);
			$note->setCreationTimestamps();
			
			$manager->persist($note);
		}
		
		$manager->flush();
	}
	
	/**
	 * @return string[]
	 */
	public function getDependencies(): array
	{
		return [
			UserFixtures::class,
		];
	}
}