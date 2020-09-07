<?php

declare(strict_types = 1);

namespace App\Tests\integration\User;

use App\Entity\User;
use App\Entity\UserPreferences;
use App\Entity\UserProfile;
use App\Event\TimeStampableCreatedEvent;
use App\Event\User\CreateEvent;
use App\Event\User\PasswordHashEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;
use DateTime;

/**
 * @coversNothing
 * Class CreateTest
 * @package App\Tests\integration\User
 */
class CreateTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testCreate()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$preferences = new UserPreferences(
			'au',
			9
		);
		
		$profile = new UserProfile(
			'axe-vatar.jpg',
			'This is a short bloody bio!!!',
			new DateTime('12.04.1987')
		);
		
		$user = new User(
			'usernameForTesting69',
			'username@testing.com',
			'User Van Namenfield',
			'sEcReTsArEf4eVRr',
			'sEcReTsArEf4eVRr',
			$preferences,
			$profile
		);
		
		$ed->dispatch(new PasswordHashEvent($user));
		
		$ed->dispatch(new CreateEvent($user));
		
		$ed->dispatch(new TimeStampableCreatedEvent($user));
		
		$em->persist($user);
		$em->flush();
		
		// assertions
		$this->tester->canSeeInRepository(
			User::class,
			[
				'username' => $user->getUsername(),
			]
		);
		
		$this->tester->canSeeInRepository(
			UserPreferences::class,
			[
				'locale' => $preferences->getLocale(),
			]
		);
		
		$this->tester->canSeeInRepository(
			UserProfile::class,
			[
				'avatar' => $profile->getAvatar(),
			]
		);
		
		static::assertNull($user->getPlainPassword());
		static::assertNull($user->getRetypedPlainPassword());
		static::assertNotEquals(
			'sEcReTsArEf4eVRr',
			$user->getPassword()
		);
	}
}