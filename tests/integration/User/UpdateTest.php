<?php

declare(strict_types = 1);

namespace App\Tests\integration\User;

use App\Entity\User;
use App\Entity\UserPreferences;
use App\Entity\UserProfile;
use App\Event\User\EmailSecurityEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @coversNothing
 * Class UpdateTest
 * @package App\Tests\integration\User
 */
class UpdateTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	/**
	 * @var EntityManagerInterface
	 */
	protected EntityManagerInterface $em;
	
	/**
	 * @var EventDispatcherInterface
	 */
	protected EventDispatcherInterface $ed;
	
	public function _before()
	{
		$this->em = $this->getEm();
		$this->ed = $this->getEd();
	}
	
	public function testUpdateUsername()
	{
		$user = $this->em->find(User::class, 1);
		
		$oldUsername = $user->getUsername();
		$newUsername = 'newÖÖÖÖÖÖÖÖÖÖÖ';
		
		$user->setUsername($newUsername);
		
		$this->em->flush();
		
		$updatedUser = $this->em->find(User::class, 1);
		
		// assertions
		self::assertEquals(
			$oldUsername,
			$updatedUser->getUsername()
		);
		
		self::assertNotEquals(
			$newUsername,
			$updatedUser->getUsername()
		);
	}
	
	public function testUpdateFullName()
	{
		$user = $this->em->find(User::class, 1);
		
		$oldFullName = $user->getFullName();
		$newFullName = 'New Full Name';
		
		$user->setFullName($newFullName);
		
		$this->em->flush();
		
		$updatedUser = $this->em->find(User::class, 1);
		
		// assertions
		self::assertEquals(
			$newFullName,
			$updatedUser->getFullName()
		);
		
		self::assertNotEquals(
			$oldFullName,
			$updatedUser->getFullName()
		);
	}
	
	public function testUpdateEmail()
	{
		$user = $this->em->find(User::class, 1);
		
		$oldEmail = $user->getEmail();
		$newEmail = 'new@email.com';
		
		$user->setEmail($newEmail);
		
		$this->ed->dispatch(
			new EmailSecurityEvent($user)
		);
		
		$this->em->flush();
		
		$updatedUser = $this->em->find(User::class, 1);
		
		// assertions
		self::assertEquals(
			$newEmail,
			$updatedUser->getEmail()
		);
		
		self::assertNotEquals(
			$oldEmail,
			$updatedUser->getEmail()
		);
		
		self::assertEquals(
			$oldEmail,
			$updatedUser->getOldEmail()
		);
	}
	
	public function testUpdatePassword()
	{
		$user = $this->em->find(User::class, 1);
		
		$oldPwdHash  = $user->getPassword();
		$newPassword = 'do not hash this test string T65';
		
		$user->setPassword($newPassword);
		
		$this->em->flush();
		
		$updatedUser = $this->em->find(User::class, 1);
		
		// assertions
		self::assertEquals(
			$newPassword,
			$updatedUser->getPassword()
		);
		
		self::assertNotEquals(
			$oldPwdHash,
			$updatedUser->getPassword()
		);
	}
	
	/**
	 * Ensure that user preferences object cannot be over written.
	 */
	public function testUpdatePreferences()
	{
		$user = $this->em->find(User::class, 1);
		
		$newPrefs = new UserPreferences('ca', 101);
		$oldPrefs = $user->getPreferences();
		
		$user->setPreferences($newPrefs);
		
		$this->em->flush();
		
		$updatedUser = $this->em->find(User::class, 1);
		
		// assertions
		self::assertEquals(
			$oldPrefs->getLocale(),
			$updatedUser->getPreferences()->getLocale()
		);
		
		self::assertEquals(
			$oldPrefs->getSortHomePageBy(),
			$updatedUser->getPreferences()->getSortHomePageBy()
		);
		
		self::assertNotEquals(
			$newPrefs->getLocale(),
			$updatedUser->getPreferences()->getLocale()
		);
		
		self::assertNotEquals(
			$newPrefs->getSortHomePageBy(),
			$updatedUser->getPreferences()->getSortHomePageBy()
		);
	}
	
	/**
	 * Ensure the user profile cannot be over written.
	 */
	public function testUpdateProfile()
	{
		$user = $this->em->find(User::class, 1);
		
		$newProf = new UserProfile('ca.webp', '101');
		$oldProf = $user->getProfile();
		
		$user->setProfile($newProf);
		
		$this->em->flush();
		
		$updatedUser = $this->em->find(User::class, 1);
		
		// assertions
		self::assertEquals(
			$oldProf->getAvatar(),
			$updatedUser->getProfile()->getAvatar()
		);
		
		self::assertEquals(
			$oldProf->getBio(),
			$updatedUser->getProfile()->getBio()
		);
		
		self::assertNotEquals(
			$newProf->getAvatar(),
			$updatedUser->getProfile()->getAvatar()
		);
		
		self::assertNotEquals(
			$newProf->getBio(),
			$updatedUser->getProfile()->getBio()
		);
	}
}